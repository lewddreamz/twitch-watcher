<?php

namespace TwitchWatcher\Services;

use Symfony\Contracts\HttpClient\ResponseInterface;
use TwitchWatcher\App\Application;
use TwitchWatcher\Collections\StreamersCollection;
use TwitchWatcher\Collections\VodsCollection;
use TwitchWatcher\Data\Condition;
use TwitchWatcher\Data\DAO\VodsDAO;
use TwitchWatcher\DateHelper;
use TwitchWatcher\Exceptions\BadRequestDataException;
use TwitchWatcher\Exceptions\VodsServiceException;
use TwitchWatcher\Services\Http;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Models\Vod;
use TwitchWatcher\VideoHelper;
use TwitchWatcher\XMLHelper;

class VodsService
{

    public function __construct(private Http $http, private VodsDAO $vodsDAO, private TwitchService $twitchService) {}

    public function getNewVodsByStreamer(Streamer $streamer): VodsCollection
    {
      $logger = Application::getLogger();
        try {
          $lastVod = ($this->vodsDAO->getLastVodOfStreamer($streamer));
        } catch (\Throwable $e) {
          $logger->error("Can't find any saved vods of streamer " . $streamer->name);
          $data = $this->twitchService->getRawVodsData($streamer);
          /**
           * @var VodsCollection
           */
          return VodsCollection::fromArray($data);
          
        }
        $data = $this->twitchService->getRawVodsData($streamer);
        /**
         * @var VodsCollection
         */
        $vods = VodsCollection::fromArray($data);
        if ($lastVod && !$vods->empty()) {
            $newVods = $vods->filter(new Condition(['uploadDate', $lastVod->uploadDate, '>']));
        }
        /**
         * @var VodsCollection $newVods
         */
        return $newVods ?? new VodsCollection();
    }

    public function getNewVodsFromTwitch(ResponseInterface $response, Streamer $streamer): VodsCollection
    {
        $arrs = XMLHelper::getLDJSON($response->getContent());
        $collection = new VodsCollection;
        $itemList = array_filter($arrs['@graph'], fn($x) => isset($x['@type']) && $x['@type'] == 'ItemList');
        try {
            $vods = $this->makeVodsFromRequest($itemList, $streamer->id);
            $collection->merge($vods);
        } catch (BadRequestDataException $e) {
            (Application::getLogger())->error($e->getMessage());
        }
        return $collection;
    }

    private function makeVodsFromRequest($itemList, ?int $streamerId = null): VodsCollection {
       
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