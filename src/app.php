<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Propel\Silex\PropelServiceProvider(), array(
    'propel.config_file'    => __DIR__ . '/../resources/propel/conf/PayMeBack-conf.php',
    'propel.model_path'     => __DIR__ . '/PayMeBack/Model',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views'
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

if(in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    $app['debug'] = true;
}

return $app;
