<?php

declare(strict_types=1);

namespace TwitchWatcher\Collections;

use Traversable;

abstract class AbstractCollection implements Traversable
{
    private $items;
}