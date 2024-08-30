<?php

namespace TwitchWatcher\Models;
//TODO попробовать аттрибуты для описания пропсов
abstract class PersistableModel extends AbstractModel
{
    protected static string $table;
    public int $id;
    public function __construct()
    {

    }

    public static function getTableName()
    {
        return self::$table;
    }
}