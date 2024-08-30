<?php

namespace TwitchWatcher\Models;

interface ModelInterface
{
    public function fill(array $attributes): true;

    public function getAttributes(): array;
    public function getValues(): array;
}