<?php

namespace TwitchWatcher\Models;

interface ModelInterface
{
    public function fill(array $attributes): true;
}