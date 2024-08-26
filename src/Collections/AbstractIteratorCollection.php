<?php

namespace TwitchWatcher\Collections;

use ArrayAccess;
use Iterator;

class AbstractIteratorCollection extends AbstractCollection implements Iterator
{
    protected int $pointer = 0;
    public function current(): mixed
    {
        return $this->items[$this->pointer];
    }
    public function key(): mixed
    {
        return $this->pointer;
    }
    public function next(): void
    {
        if (!is_null($this->items[$this->pointer])) {
            $this->pointer++;
        }
    }
    public function rewind(): void
    {
        $this->pointer = 0;
    }
    public function valid(): bool
    {
        return (!is_null($this->current()));
    }
}