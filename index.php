<?php
declare(strict_types=1);
use TwitchWatcher\App\Application;

require 'vendor/autoload.php';

$config = [
    'base_dir' => __DIR__,
    'logger' => [
        'log_dir' => 'logs',
        'verbose' => true,
        'debug' => true
    ],
    'db_filename' => 'db.sq3',
];

$app = new Application($config);

$app->run();
