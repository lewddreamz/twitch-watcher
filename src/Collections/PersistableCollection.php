<?php

namespace TwitchWatcher\Collections;

class PersistableCollection extends AbstractCollection
{
    protected static $table = '';


    public static function getTableName()
    {
        return self::$table;
    }
}