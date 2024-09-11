<?php

namespace TwitchWatcher\Models;
use TwitchWatcher\Models\PersistableModel;

class Notification extends PersistableModel
{
    protected static string $table = 'notifications';
/*id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'vod_id' INTEGER NOT NULL,
            'is_notified' BOOLEAN,
            'notification_timestamp' TIMESTAMP)");
            */
    private array $attributes = [
        'vod_id', 'is_notified', 'notification_timestamp'
    ];

    public int $vod_id;
    public bool $is_notified;
    public string $notification_timestamp;
}