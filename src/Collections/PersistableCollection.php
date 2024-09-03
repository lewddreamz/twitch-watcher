<?php

namespace TwitchWatcher\Collections;

class PersistableCollection extends AbstractCollection
{
    protected static string $table;
    public static function getTableName()
    {
        return static::$table;
    }
}