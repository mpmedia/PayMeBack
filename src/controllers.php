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
            ->joinWith('Spending')
            ->joinWith('Advance')
            ->useSpendingQuery()
                ->joinWith('SpendingCategory')
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
                'categories' => array()
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

            foreach($user['spendings'] as $spending)
            {
                $datas['users'][$userId]['categories'][$spending['SpendingCategory']['Id']]['spendings'][$spending['Id']] = array(
                    'description' => $spending['Description'],
                    'amount'      => $spending['Amount']
                );
            }

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

$app->post('/add_category', function (Request $request) use($app) {
    $spendingCategory = new \PayMeBack\Model\SpendingCategory();
    $spendingCategory->setTitle($app['request']->get('category_title'));
    $spendingCategory->save();

    return $app->redirect($app['url_generator']->generate('homepage'));
});

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
