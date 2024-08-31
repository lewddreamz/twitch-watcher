<?php

namespace TwitchWatcher\Data\DAO;

use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Models\Vod;

class VodsDAO extends AbstractDAO
{
    public function getLastVodOfStreamer(Streamer $streamer): Vod
    {
        return $this->dm->find(new Vod())
                ->where(new Condition(['streamer_id', $streamer->id, '=']))
                ->orderDesc('uploadDate')->one();
    }
}