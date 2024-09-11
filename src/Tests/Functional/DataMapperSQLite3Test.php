<?php
declare(strict_types=1);
namespace TwitchWatcher\Tests\Functional;
use PHPUnit\Framework\TestCase;
use TwitchWatcher\App\Application;
use TwitchWatcher\App\Registry;
use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Data\SQLite3DBAL;
use TwitchWatcher\Models\Vod;
class DataMapperSQLite3Test extends TestCase
{
    public $dm;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->dm = new DataMapper(new SQLite3DBAL('test-db.sql'));
    }

    public function testInsertAndSelect()
    {
        $vod = new Vod();
        $vod->id = 1;
        $vod->name = "Тестовый";
        $vod->streamer_id = 1;
        $vod->twitch_id = 'asdf';
        $vod->uploadDate = '2024-11-09 00:00:00';
        $vod->url = 'twitch.tv';
        $vod->description = 'description';
        $this->dm->insert($vod);
        $res = $this->dm->find(new Vod())->byId(1)->one();
        var_dump($res);
    }
}