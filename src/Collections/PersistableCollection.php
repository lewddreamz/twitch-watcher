<?php

namespace TwitchWatcher\Collections;

class PersistableCollection extends AbstractIteratorCollection
{
    protected static string $table;
    public static function getTableName()
    {
        return static::$table;
    }
}