<?php

namespace TwitchWatcher\Models;
//TODO попробовать аттрибуты для описания пропсов
abstract class PersistedModel extends AbstractModel
{
    private static string $table;
    protected static int $id;
    public function __construct()
    {

    }

    public static function getTableName()
    {
        return self::$table;
    }
}