<?php

declare(strict_types=1);

namespace TwitchWatcher\Collections;

use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\ModelInterface;

abstract class AbstractCollection implements ModelCollectionInterface
{
    const ASSOC = 0;
    const ARRAY = 1;
    protected array $items = [];
    /**
     * Класс элемента коллекции
     * @var 
     */
    protected string $type;

    protected bool $allowChildren = false;
    public function add(ModelInterface $item): true
    {
        if (!$this->allowChildren && ($item::class !== $this->type)) {
            throw new \InvalidArgumentException("This collection accepts only objects of {$this->type} class.");
        }
        if ($this->allowChildren && !($item instanceof $this->type)) {
            throw new \InvalidArgumentException("This collection accepts only instances of {$this->type} and its descendats.");
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

    public static function fromArray(array $values): ModelCollectionInterface
    {
        $col = new static();
        $col->fill($values);
        return $col;
    }

    #TODO сделать $items ассоциативным массивом вида id => object
    #для доступа по хэшу, тогда эту 
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
    public function getItems(?Condition $condition = null): array
    {
        if (is_null($condition)) {
            return $this->items;
        } else {
            return ($this->filter($condition))->getItems();
        }
    }

    /**
     * Returns new Collection with elements, that apply to condition
     * @param \TwitchWatcher\Data\Condition $condition
     * @return \TwitchWatcher\Collections\ModelCollectionInterface
     */
    public function filter(Condition $condition): ModelCollectionInterface
    {
        //TODO
        //так в тупую не сработает, нужно учитывать типы операндов (строка, число, дата)
        $filtered = array_filter($this->items, function($item) use ($condition) {
            $evalStr = 'return $item->' . "{$condition->leftOperand} {$condition->operator} " . '$condition->rightOperand;';
            $ret = eval($evalStr);
            return $ret;
        });
        $collection = new static();
        foreach($filtered as $el) {
            $collection->add($el);
        }
        return $collection;
    }
    public function getRawAttrs(string|array $attrs, $mode = self::ASSOC): array
    {
        if (is_string($attrs)) {
            $attrs = [$attrs];
        }
        $ret = [];
        foreach ($attrs as $attr) {
            switch($mode) {
                case (self::ASSOC):
                    $ret["$attr"] = $attr;
                    break;
                case (self::ARRAY):
                    $ret[] = $attr;
            }
        }
        return $ret;
    }

    public function empty()
    {
        return empty($this->items);
    }

    public function merge(ModelCollectionInterface $collection): ModelCollectionInterface
    {
        #TODO set get unset
        $ids1 = $this->getRawAttrs('id', self::ARRAY);
        $ids2 = $collection->getRawAttrs('id', self::ARRAY);
        if (!empty($intersect = array_intersect($ids1, $ids2))) {
            foreach($intersect as $id) {
                $this->set($id, $collection->get($id));
                $collection->unset($id);
            }
        }
        $this->items = array_merge($this->items, $collection->getItems());
    }
}