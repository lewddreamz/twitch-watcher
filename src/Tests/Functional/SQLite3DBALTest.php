<?php
declare(strict_types=1);
namespace TwitchWatcher\Tests\Functional;
use PHPUnit\Framework\TestCase;
use TwitchWatcher\Data\SQLite3DBAL;
use TwitchWatcher\Models\Vod;

class SQLite3DBALTest extends TestCase
{
    public function __construct($name
    )
    {
        parent::__construct($name);
        $this->dbal = new SQLite3DBAL('test-db.sql');
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
        $this->dbal->insert('vods', $vod->getValues());
        $select = $this->dbal->select('vods', '*');
        var_dump($select);
    }
}