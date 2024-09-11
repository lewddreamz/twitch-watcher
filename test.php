<?php
use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Data\SQLite3DBAL;
use TwitchWatcher\Services\Http;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Services\TwitchService;
use TwitchWatcher\XMLHelper;

require 'src/Services/TwitchService.php';
require 'vendor/autoload.php';

// $ts = new TwitchService(new Http());
// $dm = new DataMapper(new SQLite3DBAL('db.sq3'));
// $streamer = $dm->find(new Streamer())->byId(1)->one();
// dump($streamer);
//var_dump($ts->getRawVodsData($streamer));

$http = new Http();
$response = $http->get("https://www.twitch.tv/venomvelind/videos?filter=archives&sort=time");
$parsed = XMLHelper::getLDJSON($response->getContent());
dump($parsed);
