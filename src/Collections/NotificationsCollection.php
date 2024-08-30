<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\Notification;

class NotificationsCollection extends PersistableCollection
{
    public function __construct()
    {
        $this->type = Notification::class;
    }
}