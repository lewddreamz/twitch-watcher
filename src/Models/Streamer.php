<?php

namespace TwitchWatcher\Models;

class Streamer extends PersistedModel
{
    /*
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'name' VARCHAR NOT NULL,
            'url' VARCHAR NOT NULL)")
            */
    private static string $table = 'streamers';

    private array $attributes = [
        'name', 'url'
    ];

    private string $name;
    private string $url;
}