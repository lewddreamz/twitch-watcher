<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\Streamer;

class StreamersCollection extends PersistableCollection

{
    protected static string $table = 'streamers';
    public function __construct()
    {
        $this->type = Streamer::class;
    }
}