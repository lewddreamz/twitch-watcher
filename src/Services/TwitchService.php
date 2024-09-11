<?php
declare(strict_types=1);
namespace TwitchWatcher\Services;
use TwitchWatcher\App\Application;
use TwitchWatcher\Collections\VodsCollection;
use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Data\SQLite3DBAL;
use TwitchWatcher\DateHelper;
use TwitchWatcher\Exceptions\BadRequestDataException;
use TwitchWatcher\Services\Http;
use TwitchWatcher\Models\Streamer;
use TwitchWatcher\Models\Vod;
use TwitchWatcher\XMLHelper;

class TwitchService
{
    public function __construct(private Http $http)
    {}
    public function getRawVodsData(Streamer $streamer)
    {
        $response = $this->http->get("https://www.twitch.tv/" . $streamer->name . "/videos?filter=archives&sort=time");
        $logger = Application::getLogger();
        $collection = [];
        $arrs = XMLHelper::getLDJSON($response->getContent());
        $itemList = false;
        if ($arrs['@graph']) {
          $itemList = array_filter($arrs['@graph'], fn($x) => isset($x['@type']) && $x['@type'] == 'ItemList');
        }
        if (!empty($itemList)) {
            foreach ($itemList as $item) {
            try {
                $logger->info("Started parsing response from twitch");
                $vods = $this->makeVodsFromRequest($item, $streamer);
                if ($vods) {
                  $collection = array_merge($vods, $collection);
                }
            } catch (BadRequestDataException $e) {
                (Application::getLogger())->error($e->getMessage());
            }
          }
        }
        return $collection;
    }

    private function makeVodsFromRequest($itemList, Streamer $streamer): array|false {
      $l = Application::getLogger();
      $vods = [];
      if (!isset($itemList['itemListElement'])) {
        $l->debug("No itemlistElement in " . print_r($itemList, true));
      }
      foreach ($itemList['itemListElement'] as $item) {
        $l->debug("Processing new itemListElement..");
        if (isset($item['@type']) && $item['@type'] == 'VideoObject') {
          $l->debug('Element is videoObject');
          $matches = [];
          if (preg_match('/https:\/\/www.twitch.tv\/videos\/(.*)/',$item['url'], $matches)) {
            if (is_null($matches[1])) {
              throw new \LogicException('Нельзя добавить запись без twitchId');
            }
            $item['uploadDate'] = DateHelper::normalizeDate($item['uploadDate']);
            $item['twitch_id'] = $matches[1];
            if (!is_null($streamer->id)) {
              $item['streamer_id'] = $streamer->id;
            }

            $vods[] = $item;
            $l->info('Added vod of streamer ' . $streamer->name . ' "' . $item['name'] . '"' );
          } else {
            $l->debug($item['url'] . " didn't match mask /https:\/\/www.twitch.tv\/videos\/(.*)/");
          }
        } else {
          $l->info("item of Itemlist has type " . $item['@type'] ?? 'NULL' . ", skipping.."); 
        }
      }
      return $vods;
    }
}

