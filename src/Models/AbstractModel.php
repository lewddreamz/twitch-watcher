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

    //TODO проверка на ошибки
    public function fill(array $attributes) : void
    {
        //флаг несколько сомнительное решение, но это хотя бы означает
        //что хотя бы одно свойство было инициилизировано
        $partiallyFilled = false;
        $reflection = new \ReflectionClass(static::class);
        foreach ($attributes as $name => $value) {
            if (property_exists(static::class, "$name")) {
                // TODO подумать над системой тайпкастинга, но она должна очевидно быть не здесь,
                // скорее всего в дата маппере, либо отдельный компонент с полиморфизмом под разные дб
                // пока что этот костыль
                $reflectionProperty = $reflection->getProperty($name);
                $type = $reflectionProperty->getType()->getName();
                if ($type === 'bool') {
                    $value = (bool) $value;
                }        
                $this->$name =  $value;
                if (!$partiallyFilled) {
                    $partiallyFilled = true;
                } 
            }
        }
        if (!$partiallyFilled) {
            throw new PropertyException("Model " . static::class . " was not filled with such array of attributes " . print_r($attributes, true));
        }
    }
    public function __get($prop)
    {
        if (isset($this->$prop)) {
            return $this->$prop;
        } else {
            throw new PropertyException("No property $prop in object of class " . static::class);
        }
    }
        
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    public function getValues(): array
    {
        $values = [];
        foreach ($this->attributes as $attr) {
            if (isset($this->$attr)) {
                $values[$attr] = $this->$attr;
            } else {
                $values[$attr] = null;
            }
        }
        return $values;
    }
}