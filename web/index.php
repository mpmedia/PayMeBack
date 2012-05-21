<?php

/** @var $app \Silex\Application */
$app = require __DIR__ . '/../src/app.php';

require __DIR__ . '/../src/controllers.php';

$app->run();