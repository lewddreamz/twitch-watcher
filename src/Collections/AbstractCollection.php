<?php

declare(strict_types=1);

namespace TwitchWatcher\Collections;

use TwitchWatcher\Models\ModelInterface;

abstract class AbstractCollection implements ModelCollectionInterface
{
    private array $items;
    /**
     * Класс элемента коллекции
     * @var 
     */
    private string $type;

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
        /*
    function getItems(array|string $cond)
    function current(): mixed
    function key(): mixed
    function next(): void
    function rewind(): void
    function valid(): boolPHP
    */
    public function getItems(array|string $cond): ModelCollectionInterface
    {
        #TODO сделать searchByCondition трэйт
        return new ModelCollection();
    }


}