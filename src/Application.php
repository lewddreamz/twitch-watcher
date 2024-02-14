<?php
declare(strict_types=1);

namespace TwitchWatcher;

use Exception;

class Application
{
    private DataManager $dm;
    private Http $http;
    public function __construct() {}
    
    public function init(string $filename) : void
    {
        $this->dm = new DataManager($filename);
        $this->http = new Http;
    }

    public function run() : void
    {
        $streamers = $this->getStreamers();
        foreach($streamers as $streamer) {
            $vods = $this->getNewVods($streamer);
            if (!empty($vods)) {
                foreach($vods as $vod) {
                    $this->dm->insert('vods', $vod);
                    $id = $this->dm->queryScalar("SELECT id FROM vods ORDER BY id desc limit 1");
                    $notification = ['vod_id' => $id,
                    'is_notified' => false];
                    $this->dm->insert('notifications', $notification);
                }
            }
        }

        $notifier = new Notifier($this->dm);
        $notifications = $this->getNewNotifications();
        foreach ($notifications as $notification) {
            $notifier->notify($notification);

        }
    }

    public function getStreamers() : array|false
    {
        return $this->dm->select('streamers', '*');
    }

    public function getNewVods(array $streamer): array|false
    {
        $lastVodDate = $this->dm->queryScalar("SELECT uploadDate FROM vods
        WHERE streamer_id = '{$streamer['id']}' ORDER BY uploadDate DESC LIMIT 1");
        $h = $this->http;
        $response = $h->get("https://www.twitch.tv/{$streamer['name']}/videos?filter=archives&sort=time");
        $vods = VideoHelper::getVods($response, $streamer);
        if ($lastVodDate && !empty($vods)) {
            $vods = array_filter($vods, function($vod) use ($lastVodDate) {
                $dt1 = \DateTime::createFromFormat('Y-m-d H:i:s', $vod['uploadDate']);
                $dt2 = \DateTime::createFromFormat('Y-m-d H:i:s', $lastVodDate);
                return  $dt1 > $dt2;
            }
            );
        } 
        return !empty($vods) ? $vods : false;
    }
    
    public function getDataManager(): DataManager
    {
        if (is_null($this->dm)) {
            return $this->dm;
        }
        throw new \LogicException('DataManager not initialised');
    }

    public function getNewNotifications(): array
    {
        return $this->dm->select('notifications', '*', 'is_notified = ""');
    }
}