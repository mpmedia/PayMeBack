<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app
    ->get('/', function() use($app) {
        $categories = \PayMeBack\Model\SpendingCategoryQuery::create()
            ->orderByTitle()
            ->find()
        ;

        $dbDatas = \PayMeBack\Model\UserQuery::create()
            ->joinWith('Spending', \Criteria::LEFT_JOIN)
            ->joinWith('Advance', \Criteria::LEFT_JOIN)
            ->useSpendingQuery(null, \Criteria::LEFT_JOIN)
                ->joinWith('SpendingCategory', \Criteria::LEFT_JOIN)
            ->endUse()
            ->find()
            ->toArray()
        ;

        $datas          = array();
        $datasForCalcul = array();

        foreach($dbDatas as $user)
        {
            $userId = $user['Id'];

            $totalSpending = \PayMeBack\Model\UserPeer::getTotalSpendings($userId);
            $totalAdvance = \PayMeBack\Model\UserPeer::getTotalAdvances($userId);

            $datasForCalcul[] = array(
                'name'           => $user['Name'],
                'totalSpendings' => $totalSpending,
                'totalAdvances'  => $totalAdvance
            );
            $datas['users'][$userId] = array(
                'name'           => $user['Name'],
                'totalSpendings' => $totalSpending,
                'totalAdvances'  => $totalAdvance,
                'categories'     => array(),
                'advances'       => array()
            );

            foreach($categories as $category)
            {
                $categoryId = $category->getId();

                $datas['users'][$userId]['categories'][$categoryId] = array(
                    'title'     => $category->getTitle(),
                    'amount'    => \PayMeBack\Model\SpendingCategoryPeer::getAmountForUser($categoryId, $userId),
                    'spendings' => array()
                );
            }

            foreach($user['Spendings'] as $spending)
            {
                $datas['users'][$userId]['categories'][$spending['SpendingCategory']['Id']]['spendings'][$spending['Id']] = array(
                    'description' => $spending['Description'],
                    'amount'      => $spending['Amount']
                );
            }

            $datas['users'][$userId]['advances'] = array();

            foreach($user['Advances'] as $advance)
            {
                $datas['users'][$userId]['advances'][$advance['Id']] = array(
                    'description' => $advance['Description'],
                    'amount'      => $advance['Amount']
                );
            }
        }

        $spendingCalcul = array();

        $highSpender = $datasForCalcul[0]['totalSpendings'] > $datasForCalcul[1]['totalSpendings'] ? 0 : 1;
        $lowSpender = $datasForCalcul[0]['totalSpendings'] > $datasForCalcul[1]['totalSpendings'] ? 1 : 0;

        $interResult = $datasForCalcul[$highSpender]['totalSpendings'] - $datasForCalcul[$lowSpender]['totalSpendings'];
        $spendingResult = $interResult / 2;

        $spendingCalcul = array(
            'high' => array(
                'id'     => $highSpender,
                'name'   => $datasForCalcul[$highSpender]['name'],
                'amount' => $datasForCalcul[$highSpender]['totalSpendings']
            ),
            'low'  => array(
                'id'     => $lowSpender,
                'name'   => $datasForCalcul[$lowSpender]['name'],
                'amount' => $datasForCalcul[$lowSpender]['totalSpendings']
            ),
            'interResult' => $interResult,
            'result'      => $spendingResult
        );

        $advanceCalcul = array();

        $highAdvancer = $datasForCalcul[0]['totalAdvances'] > $datasForCalcul[1]['totalAdvances'] ? 0 : 1;
        $lowAdvancer = $datasForCalcul[0]['totalAdvances'] > $datasForCalcul[1]['totalAdvances'] ? 1 : 0;

        $advanceResult = $datasForCalcul[$highAdvancer]['totalAdvances'] - $datasForCalcul[$lowAdvancer]['totalAdvances'];

        $advanceCalcul = array(
            'high' => array(
                'id'     => $highAdvancer,
                'name'   => $datasForCalcul[$highAdvancer]['name'],
                'amount' => $datasForCalcul[$highAdvancer]['totalAdvances']
            ),
            'low'  => array(
                'id'     => $lowAdvancer,
                'name'   => $datasForCalcul[$lowAdvancer]['name'],
                'amount' => $datasForCalcul[$lowAdvancer]['totalAdvances']
            ),
            'result'      => $advanceResult
        );

        $finalCalcul = array(
            'high' => array(
                'name'   => '',
                'amount' => 0
            ),
            'low'  => array(
                'name'   => '',
                'amount' => 0
            ),
            'result'      => 0
        );

        $highFinal = $spendingResult > $advanceResult ? $lowSpender : $lowAdvancer;
        $lowFinal = $spendingResult > $advanceResult ? $lowAdvancer : $lowSpender;

        if($spendingResult > $advanceResult)
        {
            $finalCalcul['high']['name']   = $datasForCalcul[$lowSpender]['name'];
            $finalCalcul['high']['amount'] = $spendingResult;

            $finalCalcul['low']['name']   = $datasForCalcul[$lowAdvancer]['name'];
            $finalCalcul['low']['amount'] = $advanceResult;
        }
        elseif($spendingResult < $advanceResult)
        {
            $finalCalcul['high']['name']   = $datasForCalcul[$lowAdvancer]['name'];
            $finalCalcul['high']['amount'] = $advanceResult;

            $finalCalcul['low']['name']   = $datasForCalcul[$lowSpender]['name'];
            $finalCalcul['low']['amount'] = $spendingResult;
        }
        else
        {
            $finalCalcul = null;
        }

        if(null !== $finalCalcul)
        {
            $finalCalcul['result'] = $finalCalcul['high']['amount'] - $finalCalcul['low']['amount'];
        }

        return $app['twig']->render('index.twig', array(
            'datas'           => $datas,
            'spendingCalcul'  => $spendingCalcul,
            'advanceCalcul'   => $advanceCalcul,
            'finalCalcul'     => $finalCalcul
        ));
    })
    ->bind('homepage')
;

$app
    ->post('/add_category', function (Request $request) use($app) {
        $app['session']->start();

        try
        {
            $spendingCategory = new \PayMeBack\Model\SpendingCategory();
            $spendingCategory
                ->setTitle($app['request']->get('category_title'))
                ->save()
            ;

            $app['session']->setFlash('success', 'Catégorie crée avec succès');
        }
        catch(\Exception $exception)
        {
            $app['session']->setFlash('error', 'Erreur lors de l\'ajout de la catégorie');
        }

        return $app->redirect($app['url_generator']->generate('homepage'));
    })
    ->bind('add_category')
;

$userProvider = function ($id) {
    $user = \PayMeBack\Model\UserQuery::create()->findOneById($id);

    if(null === $user)
    {
        throw new \Exception('User can\'t be retrieve.');
    }

    return $user;
};

$spendingCategoryProvider = function ($id) {
    $spendingCategory = \PayMeBack\Model\SpendingCategoryQuery::create()->findOneById($id);

    if(null === $spendingCategory)
    {
        throw new \Exception('Category can\'t be retrieve.');
    }

    return $spendingCategory;
};

$spendingProvider = function ($id) {
    $spending = \PayMeBack\Model\SpendingQuery::create()->findOneById($id);

    if(null === $spending)
    {
        throw new \Exception('Spending can\'t be retrieve.');
    }

    return $spending;
};

$advanceProvider = function ($id) {
    $advance = \PayMeBack\Model\AdvanceQuery::create()->findOneById($id);

    if(null === $advance)
    {
        throw new \Exception('Advance can\'t be retrieve.');
    }

    return $advance;
};

$app
    ->get('/add_spending/{user}/{category}', function (Request $request, \PayMeBack\Model\User $user, \PayMeBack\Model\SpendingCategory $category) use($app) {
        $spending = new \PayMeBack\Model\Spending();
        $spending
            ->setDescription($app['request']->get('spending_description'))
            ->setAmount($app['request']->get('spending_amount'))
            ->setSpendingCategory($category)
            ->setUser($user)
            ->save()
        ;

        return $app->redirect($app['url_generator']->generate('homepage'));
    })
    ->bind('add_spending')
    ->convert('user', $userProvider)
    ->convert('category', $spendingCategoryProvider)
;

$app
    ->get('/add_advance/{user}', function (Request $request, \PayMeBack\Model\User $user) use($app) {
        $advance = new \PayMeBack\Model\Advance();
        $advance
            ->setDescription($app['request']->get('advance_description'))
            ->setAmount($app['request']->get('advance_amount'))
            ->setUser($user)
            ->save()
        ;

        return $app->redirect($app['url_generator']->generate('homepage'));
    })
    ->bind('add_advance')
    ->convert('user', $userProvider)
;

$app
    ->get('/delete_spending/{spending}', function (Request $request, \PayMeBack\Model\Spending $spending) use($app) {
        $spending->delete();

        return $app->redirect($app['url_generator']->generate('homepage'));
    })
    ->bind('delete_spending')
    ->convert('spending', $spendingProvider)
;

$app
    ->get('/delete_advance/{advance}', function (Request $request, \PayMeBack\Model\Advance $advance) use($app) {
        $advance->delete();

        return $app->redirect($app['url_generator']->generate('homepage'));
    })
    ->bind('delete_advance')
    ->convert('advance', $advanceProvider)
;

// Error Handler
$app->error(function(\Exception $e, $code) use($app) {
    if($app['debug']) {
        return;
    }

    switch($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message, $code);
});

return $app;
