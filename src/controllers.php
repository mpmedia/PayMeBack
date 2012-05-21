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
