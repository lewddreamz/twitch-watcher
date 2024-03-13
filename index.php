<?php
declare(strict_types=1);
use TwitchWatcher\Application;

require 'vendor/autoload.php';

$config = [
    'base_dir' => __DIR__,
    'logger' => [
        'log_dir' => 'logs',
        'verbose' => true,
        'debug' => true
    ],
];

$app = new Application($config);

$app->init(__DIR__ . '/db.sq3');
$app->run();
