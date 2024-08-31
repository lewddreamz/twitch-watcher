<?php

namespace TwitchWatcher\Data\DAO;

use TwitchWatcher\Collections\NotificationsCollection;
use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\Notification;

class NotificationsDAO extends AbstractDAO
{
    public function getNewNotifications(): NotificationsCollection
    {
        return $this->dm->find(new NotificationsCollection())
                ->where(new Condition('is_notified=false'))
                ->all();
    }
}