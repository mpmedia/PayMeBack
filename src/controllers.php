<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app
    ->get('/', function() use($app) {
        $users = \PayMeBack\Model\UserQuery::create()
            ->find()
        ;
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
            $datas['users'][$user['Id']] = array(
                'name'       => $user['Name'],
                'categories' => array()
            );

            $userId = $user['Id'];

            foreach($categories as $category)
            {
                $datas['users'][$userId]['categories'][$category->getId()] = array(
                    'title'     => $category->getTitle(),
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

            foreach($user['Advances'] as $advance)
            {
                $datas['users'][$userId]['advances'][$advance['Id']] = array(
                    'description' => $advance['Description'],
                    'amount'      => $advance['Amount']
                );
            }
        }

//    echo "<pre>";
//    var_dump($dbDatas[0]);
//    echo "</pre>" . PHP_EOL;
    echo "<pre>";
    var_dump($datas);
    echo "</pre>" . PHP_EOL;
    die("FFFFFUUUUUCCCCCKKKKK" . PHP_EOL);

        return $app['twig']->render('index.twig', array(
            'users'      => $users,
            'categories' => $categories
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
