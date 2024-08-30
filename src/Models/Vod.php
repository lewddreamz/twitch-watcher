<?php

namespace TwitchWatcher\Models;

class Vod extends PersistableModel

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
    #TODO свои атрибуты
    //#[\Property]
    public string $name;
    public string $description;
    public string $uploadDate;
    public string $twitch_id;
    public string $url;
    public string $streamer_id;

}