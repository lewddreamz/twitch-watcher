<?php

namespace TwitchWatcher\Collections;
use Iterator;

class AbstractIteratorCollection extends AbstractCollection implements Iterator
{
    private int $pointer = 0;
    public function current(): mixed
    {
        return current($this->items);
    }
    public function key(): mixed
    {
        return key($this->items);
    }
    public function next(): void
    {
        next($this->items);
        // $this->pointer++;
    }
    public function rewind(): void
    {
        $this->pointer = 0;
    }
    public function valid(): bool
    {
        return (! is_null($this->current()));
    }
}