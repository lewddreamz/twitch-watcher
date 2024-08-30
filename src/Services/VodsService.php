<?php

namespace TwitchWatcher\Services;

use TwitchWatcher\App\Application;
use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Models\Vod;
use TwitchWatcher\VideoHelper;

class VodsService
{

    public function getNewVodsByStreamer(Streamer $streamer)
    {
        $reg = Application::getRegistry();
        $dm = $reg->getDataMapper();
        $h = $reg->getHttp();
        $lastVod = $dm->find(new Vod())->where(new Condition(['streamer_id', $streamer->id, '=']))->orderBy('uploadDate', 'desc')->one();

        $response = $h->get("https://www.twitch.tv/{$streamer['name']}/videos?filter=archives&sort=time");

        $vods = VideoHelper::getVods($response, $streamer);
        if ($lastVod && !$vods->empty()) {
            $vods = array_filter($vods, function($vod) use ($lastVod) {
                $dt1 = \DateTime::createFromFormat('Y-m-d H:i:s', $vod->uploadDate);
                $dt2 = \DateTime::createFromFormat('Y-m-d H:i:s', $lastVod->uploadDate);
                return  $dt1 > $dt2;
            }
            );
        }
        return !empty($vods) ? $vods : false;

    }
}