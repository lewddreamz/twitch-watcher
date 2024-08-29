<?php

namespace TwitchWatcher\Collections;

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
    public function getItems(array|string $cond): ModelCollectionInterface|false;
    public function getRawAttrs(string|array $attr, int $mode): array;
}