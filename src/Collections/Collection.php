<?php

declare(strict_types=1);

namespace TwitchWatcher\Collections;

use Traversable;

abstract class Collection implements Traversable
{
    private $items;
}