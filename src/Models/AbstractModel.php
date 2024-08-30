<?php

declare(strict_types=1);

namespace TwitchWatcher\Models;

use DeepCopy\Exception\PropertyException;
use InvalidArgumentException;
use TwitchWatcher\Application;
use TwitchWatcher\Data\DataInfo;
use TwitchWatcher\Data\EntityManager;

class AbstractModel implements ModelInterface
{
    protected array $attributes;

    public function fill(array $attributes) : true
    {
        return true;
    }
    /*
    public function __get($prop)
    {
        if (isset($this->$prop)) {
            return $this->$prop;
        } else {
            throw new PropertyException("No property $prop in object of class " . static::class);
        }
    }
        */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    public function getValues(): array
    {
        $vals = [];
        foreach ($this->attributes as $attr) {
            $vals[$attr] = $this->$attr;
        }
        return $vals;
    }
}