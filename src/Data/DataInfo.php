<?php

declare(strict_types=1);

namespace TwitchWatcher\Data;

class DataInfo
{
    private string $table;

    public function getTable()
    {
        return $this->table;
    }
}