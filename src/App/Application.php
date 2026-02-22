<?php

declare(strict_types=1);

namespace TwitchWatcher\App;

use TwitchWatcher\Data\DAO\NotificationsDAO;
use TwitchWatcher\Data\DAO\VodsDAO;
use TwitchWatcher\Exceptions\NotInitializedException;
use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Services\Http;
use TwitchWatcher\Logger;
use TwitchWatcher\Collections\PersistableCollection;
use TwitchWatcher\Collections\StreamersCollection;
use TwitchWatcher\Models\Notification;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Models\Vod;
use TwitchWatcher\Notifier;
use TwitchWatcher\Services\VodsService;

class Application
{
    private DataMapper $dm;
    private Http $http;
    public static ?Logger $logger = null;
    private static ?self $instance = null;
    public Config $config;

    /**
     * @param array<int,mixed> $config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
        self::$instance = $this;
        $reg = Registry::instance($this->config);
        $this->dm = $reg->getDataMapper();
        $this->http = $reg->getHttp();
    }

    public static function instance(): self
    {
        if (null === self::$instance) {
            throw new NotInitializedException('Приложение еще не было инициализировано');
        }

        return self::$instance;

    }

    public static function config(): Config
    {
        if (null !== self::$instance) {
            return self::$instance->config;
        }

        throw new NotInitializedException('Попытка получить конфиг до инициализации приложения');

    }

    public static function getRegistry(): Registry
    {
        return Registry::instance();
    }

    public static function getLogger(): Logger
    {
        if (null === self::$logger) {
            self::$logger = new Logger(Application::config());

            return self::$logger;
        }

        return self::$logger;

    }

    /**
     * @param array<int,mixed> $options
     */
    public function setConfig(array $options): void
    {
        $this->config = new Config($options);
    }

    public function run(): void
    {
        $log = self::getLogger();
        $dm = $this->dm;
        $reg = self::getRegistry();
        $log->info('Начинаем запрос водов');
        $log->info('Получаем список стримеров...');
        $vodService = new VodsService($reg->getHttp(), new VodsDAO($reg->getDataMapper()), $reg->getTwitchService());

        /**
         * @var PersistableCollection
         */
        $streamers = $dm->find(new StreamersCollection())->do();
        $totalVodsCount = 0;
        // @var Streamer
        foreach ($streamers as $streamer) {
            $log->info('Ищем новые воды для стримера ' . $streamer->name);
            $vods = $vodService->getNewVodsByStreamer($streamer);
            if (!$vods->empty()) {
                $vodsCount = 0;
                foreach ($vods as $vod) {
                    $log->info('Сохранение вода vod_id ' . $vod->name . ' стримера ' . $streamer->name);
                    $dm->insert($vod);
                    // TODO избавиться от этого, модель должна обновлять id сама после вставки
                    $vod = $this->dm->find(new Vod())->last();
                    $notification = new Notification();
                    $notification->vod_id = $vod->id;
                    $notification->is_notified = false;
                    $this->dm->insert($notification);
                    $log->info('Вод сохранен успешно!');
                    ++$totalVodsCount;
                    ++$vodsCount;
                }
                $log->info('Для стримера ' . $streamer->name . " было сохранено {$vodsCount} новых водов");
            } else {
                $log->info('Нет новых водов для стримера ' . $streamer->name);
            }
            usleep(500000);
        }
        $log->info("Было добавлено {$totalVodsCount} новых водов");

        $notifier = new Notifier($reg->getDataMapper());

        $log->info('Получаем список новых оповещений');
        $notificationsDao = new NotificationsDAO($reg->getDataMapper());
        $notifications = $notificationsDao->getNewNotifications();

        if ($notifications->empty()) {
            $log->info('Нет новых оповещений!');
        }
        foreach ($notifications as $notification) {
            $notifier->notify($notification);
        }
    }
}
