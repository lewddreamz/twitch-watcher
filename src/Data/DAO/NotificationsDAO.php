<?php

namespace TwitchWatcher\Data\DAO;

use TwitchWatcher\App\Application;
use TwitchWatcher\Collections\NotificationsCollection;
use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\Notification;

class NotificationsDAO extends AbstractDAO
{
    public function getNewNotifications(): NotificationsCollection
    {
        try {
             $this->dm->find(new NotificationsCollection())
                ->where(new Condition('is_notified=false'))
                ->all();
        } catch (\Exception $e) {
            Application::getLogger()->info("No new notifications");
            return new NotificationsCollection();
        }
    }
}