<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\Notification;

class NotificationsCollection extends PersistableCollection
{
    protected static string $table = 'notifications';

    public function __construct()
    {
        $this->type = Notification::class;
    }
}