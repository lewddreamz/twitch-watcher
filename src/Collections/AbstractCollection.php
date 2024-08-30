<?php

declare(strict_types=1);

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\ModelInterface;

abstract class AbstractCollection implements ModelCollectionInterface
{
    const ASSOC = 0;
    const ARRAY = 1;
    protected array $items;
    /**
     * Класс элемента коллекции
     * @var 
     */
    protected string $type;

    public function add(ModelInterface $item): true
    {
        if ($item::class !== $this->type) {
            throw new \InvalidArgumentException("This collection accepts only objects of {$this->type} class.");
        }
        $this->items[] = $item;
        return true;
    }
    public function fill(array $values): true
    {
        foreach ($values as $value) {
            $obj = new $this->type();
            $obj->fill($value);
            $this->add($obj);
        }
        return true;
    }

    public function getItem(int $id): ModelInterface|false
    {
        $filtered = array_filter($this->items, fn($el) => $el->id == $id);
        if (count($filtered) > 1) {
            throw new \LogicException("Collection contains non-unique members");
        }
        if (!empty($filtered)) {
            return $filtered[0];
        } else {
            return false;
        }
    }

    public function getItems(array|string $cond): ModelCollectionInterface
    {
        #TODO сделать searchByCondition трэйт, поиск по условиям, может пригодиться в коллекциях и в поиске в бд
        return new ModelCollection();
    }

    public function getRawAttrs(string|array $attrs, $mode = static::ASSOC): array
    {
        if (is_string($attrs)) {
            $attrs = [$attrs];
        }
        $ret = [];
        foreach ($attrs as $attr) {
            switch($mode) {
                case (static::ASSOC):
                    $ret["$attr"] = $attr;
                    break;
                case (static::ARRAY):
                    $ret[] = $attr;
            }
        }
        return $ret;
    }

}