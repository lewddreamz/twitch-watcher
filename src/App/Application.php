<?php
declare(strict_types=1);

namespace TwitchWatcher\App;

use Exception;
use LogicException;
use TwitchWatcher\Collections\ModelCollection;
use TwitchWatcher\Exceptions\NotInitializedException;
use TwitchWatcher\Data\DataMapper; 
use TwitchWatcher\Http;
use TwitchWatcher\Logger;
use TwitchWatcher\App\Registry;
use TwitchWatcher\Collections\PersistableCollection;
use TwitchWatcher\Collections\StreamersCollection;
use TwitchWatcher\Models\Notification;
use TwitchWatcher\Models\PersistableModel;
use TwitchWatcher\Models\Vod;
use TwitchWatcher\Notifier;
use TwitchWatcher\VideoHelper;

class Application
{
    private DataMapper $dm;
    private Http $http;
    public static ?Logger $logger = null;

    private static ?self $instance = null;
    public Config $config;

    public function __construct(array $config) 
    {
        $this->setConfig($config);
        self::$instance = $this;
        $reg = Registry::instance();
        $this->dm = $reg->getDataMapper();
        $this->http = $reg->getHttp();
    }
    
    public static function instance(): self
    {
        if (self::$instance === null) {
            throw new NotInitializedException("Приложение еще не было инициализировано");
        } else {
            return self::$instance;
        }
    }

    public static function config(): Config
    {
        if (self::$instance !== null) {
            return self::$instance->config;
        } else {
            throw new NotInitializedException("Попытка получить конфиг до инициализации приложения");
        }
    }

    public static function getRegistry(): Registry
    {
        return Registry::instance();
    }
    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger();
            return self::$logger;
        } else {
            return self::$logger;
        }
    }
    public function setConfig(array $options): void
    {
        $this->config = new Config($options);
    }

    public function run() : void
    {
        $log = self::getLogger();
        $dm = $this->dm;
        $log->info("Начинаем запрос водов");
        $log->info("Получаем список стримеров...");

        /**
         * @var PersistableCollection
         */
        $streamers = $dm->find(new StreamersCollection)->do();
        /**
         * @var PersistableModel
         */
        foreach($streamers as $streamer) {
            $log->info("Ищем новые воды для стримера " . $streamer->name);
            $vods = $this->getNewVods($streamer);
            $vods = VodsService::getNewVodsOfStreamer($streamer);
            $totalVodsCount = 0;
            if (!empty($vods)) {
                $vodsCount = 0;
                foreach($vods as $vod) {
                    $log->info("Сохранение вода vod_id " . $vod->name . " стримера " . $streamer->name);
                    $dm->insert($vod);
                    $vod = $this->dm->find(new Vod)->last();
                    $notification = new Notification();
                    $notification->vod_id = $vod->id;
                    $notification->is_notified = false;
                    $this->dm->insert($notification);
                    $log->info("Вод сохранен успешно!");
                    $totalVodsCount++;
                    $vodsCount++;
                }
                $log->info("Для стримера " . $streamer->name . " было сохранено $vodsCount новых водов");
            } else {
                $log->info("Нет новых водов для стримера " . $streamer->name);
            }
            usleep(500000);
        }
        $log->info("Было добавлено $totalVodsCount новых водов");

        $notifier = new Notifier();
        $log->info('Получаем список новых оповещений');
        $notifications = $this->getNewNotifications();
        if (empty($notifications)) {
            $log->info("Нет новых оповещений!");
        }
        foreach ($notifications as $notification) {
            $notifier->notify($notification);

        }
    }
    
    public function getNewNotifications(): array
    {
        return $this->dm->select('notifications', '*', 'is_notified = ""');
    }


}
