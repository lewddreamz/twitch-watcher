<?php

declare(strict_types=1);

namespace TwitchWatcher\Models;

use InvalidArgumentException;
use TwitchWatcher\Application;
use TwitchWatcher\Data\DataInfo;
use TwitchWatcher\Data\EntityManager;

class AbstractModel implements ModelInterface
{
    private array $attributes;

    public function fill(array $attributes) : true
    {
        return true;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}