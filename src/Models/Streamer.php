<?php

namespace TwitchWatcher\Models;

class Streamer extends PersistableModel
{
    /*
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'name' VARCHAR NOT NULL,
            'url' VARCHAR NOT NULL)")
            */
    private static string $table = 'streamers';

    protected array $attributes = [
        'name', 'url'
    ];

    private string $name;
    private string $url;
}