<?php

namespace TwitchWatcher\App;

use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Data\SQLite3DBAL;
use TwitchWatcher\App\Application as App;
use TwitchWatcher\Services\Http;
use TwitchWatcher\Services\TwitchService;
#TODO додумоть доделоть
class Registry
{
    public static $instance = null;

    public function __construct(private Config $config) {}
    public static function instance(?Config $config = null)
    {
        if (self::$instance == null) {
            if (($config) === null) {
                throw new \RuntimeException("Can't create new Registry without provided config");
            }
            $reg = new Registry($config);
            self::$instance = $reg;
        }
        return self::$instance;
    }
    
    public function getDataMapper(): DataMapper {
        $dbalClass = $this->config->dbal_class;
        $methodName = "get" . ucfirst($dbalClass);
        if (!method_exists(self::class, $methodName)) {
            throw new \BadMethodCallException("Can't call method $methodName");
        }
        $dbal = call_user_func([self::class, $methodName]);
        return new DataMapper($dbal);
    }

    public function getSQLite3DBAL(): SQLite3DBAL {
        return new SQLite3DBAL($this->config->db_filename);
    }

    public function getHttp(): Http {
        return new Http();
    }

    public function getTwitchService(): TwitchService
    {
        return new TwitchService($this->getHttp());
    }
}