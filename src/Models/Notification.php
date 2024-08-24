<?php

namespace TwitchWatcher\Models;
use TwitchWatcher\Models\PersistedModel;

class Notification extends PersistedModel
{
    private static string $table = 'notifications';
/*id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'vod_id' INTEGER NOT NULL,
            'is_notified' BOOLEAN,
            'notification_timestamp' TIMESTAMP)");
            */
    private array $attributes = [
        'vod_id', 'is_notified', 'notification_timestamp'
    ];

}