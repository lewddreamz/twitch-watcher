<?php

namespace TwitchWatcher\Collections;

use TwitchWatcher\Data\Condition;
use TwitchWatcher\Models\ModelInterface;

interface ModelCollectionInterface
{
    public function fill(array $values);

    /**
     * Добавление элемента в коллекцию.
     *
     * @throws \Throwable
     */
    public function add(ModelInterface $item): true;

    public function getItem(int $id): false|ModelInterface;

    public function getItems(): array;

    public function getRawAttrs(array|string $attr, int $mode): array;

    /**
     * Merge with another collection.
     *
     * @param mixed $collection
     */
    public function merge(ModelCollectionInterface $collection): ModelCollectionInterface;

    public function filter(Condition $condition): ModelCollectionInterface;
}
