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
        $log->info("Начинаем запрос водов");
        $log->info("Получаем список стримеров...");

        $streamers = $this->getStreamers();
        foreach($streamers as $streamer) {
            $log->info("Ищем новые воды для стримера {$streamer['name']}");
            $vods = $this->getNewVods($streamer);
            $totalVodsCount = 0;
            if (!empty($vods)) {
                $vodsCount = 0;
                foreach($vods as $vod) {
                    $log->info("Сохранение вода vod_id {$vod['name']}, стримера {$streamer['name']}");
                    $this->dm->insert('vods', $vod);
                    $id = $this->dm->queryScalar("SELECT id FROM vods ORDER BY id desc limit 1");
                    $notification = ['vod_id' => $id,
                    'is_notified' => false];
                    $this->dm->insert('notifications', $notification);
                    $log->info("Вод сохранен успешно!");
                    $totalVodsCount++;
                    $vodsCount++;
                }
                $log->info("Для стримера {$streamer['name']} было сохранено $vodsCount новых водов");
            } else {
                $log->info("Нет новых водов для стримера {$streamer['name']}");
            }
            usleep(500000);
        }
        $log->info("Было добавлено $totalVodsCount новых водов");

        $notifier = new Notifier($this->dm);
        $log->info('Получаем список новых оповещений');
        $notifications = $this->getNewNotifications();
        if (empty($notifications)) {
            $log->info("Нет новых оповещений!");
        }
        foreach ($notifications as $notification) {
            $notifier->notify($notification);

        }
    }

    public function getStreamers() : array|false
    {
        return $this->dm->select('streamers', '*');
    }

    public function getNewVods(array $streamer): array|false
    {
        $lastVodDate = $this->dm->queryScalar("SELECT uploadDate FROM vods
        WHERE streamer_id = '{$streamer['id']}' ORDER BY uploadDate DESC LIMIT 1");
        $h = $this->http;
        $response = $h->get("https://www.twitch.tv/{$streamer['name']}/videos?filter=archives&sort=time");

        $vods = VideoHelper::getVods($response, $streamer);
        if ($lastVodDate && !empty($vods)) {
            $vods = array_filter($vods, function($vod) use ($lastVodDate) {
                $dt1 = \DateTime::createFromFormat('Y-m-d H:i:s', $vod['uploadDate']);
                $dt2 = \DateTime::createFromFormat('Y-m-d H:i:s', $lastVodDate);
                return  $dt1 > $dt2;
            }
            );
        }
        return !empty($vods) ? $vods : false;
    }
    
    public function getDataManager(): DataManager
    {
        if (is_null($this->dm)) {
            return $this->dm;
        }
        throw new \LogicException('DataManager not initialised');
    }

    public function getNewNotifications(): array
    {
        return $this->dm->select('notifications', '*', 'is_notified = ""');
    }


}
