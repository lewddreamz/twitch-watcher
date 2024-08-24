<?php

namespace TwitchWatcher\App;

use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Data\SQLite3DBAL;
use TwitchWatcher\App\Application as App;
use TwitchWatcher\Http;

class Registry
{
    public static $instance = null;
    public static function instance()
    {
        if (self::$instance == null) {
            $reg = new Registry();
            self::$instance = $reg;
        }
        return self::$instance;
    }
    
    public function getDataMapper(): DataMapper {
        $cfg = Application::config();
        $dbalClass = $cfg->dbal_class;
        $methodName = "::get" . ucfirst($dbalClass);
        if (!method_exists(self::class, $methodName)) {
            throw new \BadMethodCallException();
        }
        $dbal = call_user_func(self::class . $methodName);
        return new DataMapper($dbal);
    }

    public function getSQLite3DBAL(): SQLite3DBAL {
        $cfg = App::config();
        return new SQLite3DBAL($cfg->db_filename);
    }

    public function getHttp(): Http {
        return new Http();
    }
}