<?php

declare(strict_types=1);

namespace TwitchWatcher\Models;

use InvalidArgumentException;
use TwitchWatcher\Application;
use TwitchWatcher\Data\DataInfo;
use TwitchWatcher\Data\EntityManager;

class AbstractModel implements ModelInterface
{
    private static DataInfo $dataInfo;

   

    public static function getDataInfo(): DataInfo
    {
        return self::$dataInfo;
    }
}