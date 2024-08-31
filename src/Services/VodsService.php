<?php

namespace TwitchWatcher\Services;

use Symfony\Contracts\HttpClient\ResponseInterface;
use TwitchWatcher\App\Application;
use TwitchWatcher\Collections\VodsCollection;
use TwitchWatcher\Data\Condition;
use TwitchWatcher\Data\DAO\VodsDAO;
use TwitchWatcher\DateHelper;
use TwitchWatcher\Exceptions\BadRequestDataException;
use TwitchWatcher\Http;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Models\Vod;
use TwitchWatcher\VideoHelper;
use TwitchWatcher\XMLHelper;

class VodsService
{

    public function __construct(private Http $http, private VodsDAO $vodsDAO) {}

    public function getNewVodsByStreamer(Streamer $streamer): VodsCollection
    {
        $lastVod = ($this->vodsDAO->getLastVodOfStreamer($streamer));

        $response = $this->http->get("https://www.twitch.tv/" . $streamer->name . "/videos?filter=archives&sort=time");

        $vods = $this->getNewVodsFromTwitch($response, $streamer);
        
        if ($lastVod && !$vods->empty()) {
            /*$vods = array_filter($vods, function($vod) use ($lastVod) {
                $dt1 = \DateTime::createFromFormat('Y-m-d H:i:s', $vod->uploadDate);
                $dt2 = \DateTime::createFromFormat('Y-m-d H:i:s', $lastVod->uploadDate);
                return  $dt1 > $dt2;
            }
            );
            */
            $newVods = $vods->filter(new Condition(['uploadDate', $lastVod->uploadDate, '>']));
        }
        /**
         * @var VodsCollection $newVods
         */
        return $newVods;
    }

    public function getNewVodsFromTwitch(ResponseInterface $response, Streamer $streamer): VodsCollection
    {
        $arrs = XMLHelper::getLDJSON($response->getContent());
        $collection = new VodsCollection;
        foreach ($arrs as $arr) {
            foreach($arr as $itemList) {
                try {
                    $vods = $this->makeVodsFromRequest($itemList, $streamer->id);
                    $collection->merge($vods);
                } catch (BadRequestDataException $e) {
                    (Application::getLogger())->error($e->getMessage());
                }
            }
        }
        return $collection;
    }

    private function makeVodsFromRequest($itemList, ?int $streamerId = null): VodsCollection {
        if (!isset($itemList['@type']) || $itemList['@type'] != 'ItemList') {
          throw new BadRequestDataException ("Malformed response from twitch");
        }
        $vods = new VodsCollection;
        foreach ($itemList['itemListElement'] as $item) {
          if (isset($item['@type']) && $item['@type'] == 'VideoObject') {
            $matches = [];
            if (preg_match('/https:\/\/www.twitch.tv\/videos\/(.*)/',$item['url'], $matches)) {
              if (is_null($matches[1])) {
                throw new \LogicException('Нельзя добавить запись без twitchId');
              }
              $item['uploadDate'] = DateHelper::normalizeDate($item['uploadDate']);
              $item['twitch_id'] = $matches[1];
              if (!is_null($streamerId)) {
                $item['streamer_id'] = $streamerId;
              }

              $vod = new Vod;
              $vod->fill($item);
              $vods->add($vod);        
            }
          }
        }
        return $vods;
      }
}