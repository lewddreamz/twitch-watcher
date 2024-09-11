<?php

namespace TwitchWatcher\Models;
//TODO попробовать аттрибуты для описания пропсов
abstract class PersistableModel extends AbstractModel
{
    /**
     * This prop is declared static if table name needed without object creation overhead
     * @var string
     */
    protected static string $table;
    public int $id;
    public function __construct()
    {

    }

    public static function getTableName()
    {
        return static::$table;
    }
}