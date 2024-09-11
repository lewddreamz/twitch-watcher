<?php

namespace TwitchWatcher\Models;

interface ModelInterface
{
    /**
     * Fill Model attributes from associative array $propName => $propValue
     * @param array $attributes
     * @throws \Exception on failed fill
     * @return void
     */
    public function fill(array $attributes): void;

    public function getAttributes(): array;
    public function getValues(): array;
}