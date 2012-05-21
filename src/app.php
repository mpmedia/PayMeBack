<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Propel\Silex'  => __DIR__ . '/../vendor/propel/propel-service-provider/src',
    'PayMeBack'     => __DIR__ . '/../src',
));

$app->register(new Propel\Silex\PropelServiceProvider(), array(
    'propel.path'           => __DIR__ . '/../vendor/propel/propel1/runtime/lib',
    'propel.config_file'    => __DIR__ . '/../resources/propel/conf/PayMeBack-conf.php',
    'propel.model_path'     => __DIR__ . '/../src/PayMeBack/Model',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'             => __DIR__ . '/../views',
    'twig.class_path'       => __DIR__ . '/../vendor/twig/twig/lib',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SymfonyBridgesServiceProvider());

if(in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    $app['debug'] = true;
}

return $app;
