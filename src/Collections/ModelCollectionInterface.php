<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\ModelInterface;

interface ModelCollectionInterface
{
    public function fill(array $values);
    /**
     * Добавление элемента в коллекцию
     * @throws \Throwable
     * @param \TwitchWatcher\Models\ModelInterface $item
     * @return true
     */
    public function add(ModelInterface $item): true;
    public function getItem(int $id): ModelInterface|false;
    public function getItems(): array;
    public function getRawAttrs(string|array $attr, int $mode): array;
    /**
     * Merge with another collection
     * @param mixed $collection
     * @return void
     */
    public function merge(ModelCollectionInterface $collection): ModelCollectionInterface;
    public function filter(Condition $condition): ModelCollectionInterface;
}