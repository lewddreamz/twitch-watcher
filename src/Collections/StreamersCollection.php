<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\Streamer;

class StreamersCollection extends PersistableCollection

{
    public function __construct()
    {
        $this->type = Streamer::class;
    }
}