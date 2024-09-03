<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\Vod;

class VodsCollection extends PersistableCollection
{
    protected static string $table = 'vods';

    public function __construct()
    {
        $this->type = Vod::class;
    }
}