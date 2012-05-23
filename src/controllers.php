<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app
    ->get('/', function() use($app) {
        $categories = \PayMeBack\Model\SpendingCategoryQuery::create()
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

        $datas = array();
        foreach($dbDatas as $user)
        {
            $userId = $user['Id'];

            $datas['users'][$userId] = array(
                'name'       => $user['Name'],
                'totalSpendings' => \PayMeBack\Model\UserPeer::getTotalSpendings($userId),
                'totalAdvances' => \PayMeBack\Model\UserPeer::getTotalAdvances($userId),
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

        return $app['twig']->render('index.twig', array(
            'datas'      => $datas
        ));
    })
    ->bind('homepage')
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
