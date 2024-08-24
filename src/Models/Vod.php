<?php

namespace TwitchWatcher\Models;

class Vod extends PersistedModel

{
    private static string $table = 'vods';

    /*'name' VARCHAR NOT NULL,
            'description' VARCHAR NOT NULL,
            'uploadDate' DATETIME NOT NULL, 
            'twitch_id' VARCHAR NOT NULL,
            'url' VARCHAR NOT NULL,
            'streamer_id
            */
    public array $attributes = [
        'name', 'description',
        'uploadDate',
        'twitch_id',
        'url',
        'streamer_id'
    ];

    //#[\Property]
    private string $name;
    private string $description;
    private string $uploadDate;
    private string $twitch_id;
    private string $url;
    private string $streamer_id;

}