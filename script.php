<?php
declare(strict_types=1);
use TwitchWatcher\Application;

require 'vendor/autoload.php';

$app = new Application;

$app->init(__DIR__ . '/db.sq3');
$app->run();


