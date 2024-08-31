<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\Vod;

class VodsCollection extends PersistableCollection
{
    public function __construct()
    {
        $this->type = Vod::class;
    }
}