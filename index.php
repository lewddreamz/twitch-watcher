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
    'dbal_class'  => 'SQLite3DBAL'
];

$app = new Application($config);

$app->run();
