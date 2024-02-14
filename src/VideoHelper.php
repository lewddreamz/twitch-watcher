<?php

declare(strict_types=1);

namespace TwitchWatcher;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VideoHelper
{
    public static function getVods(ResponseInterface $response, ?array $streamer = null): array|false
    {
        $arrs = XMLHelper::getLDJSON($response->getContent());
        $collection = [];
        foreach ($arrs as $arr) {
            foreach($arr as $itemList) {
                if (($vods = self::makeVideoObjectsCollectionFromRequest($itemList, $streamer['id'])) && is_array($vods)) {
                    $collection[] = $vods;
                }
            }
        }
        if (!empty($collection)) {
            return array_merge(...$collection);
        } else {
            return false;
        }
    }

    private static function makeVideoObjectsCollectionFromRequest($itemList, ?int $streamerId = null):array|false {
        if (!isset($itemList['@type']) || $itemList['@type'] != 'ItemList') {
          return false;
        }
        $collection = [];
        foreach ($itemList['itemListElement'] as $item) {
          if (isset($item['@type']) && $item['@type'] == 'VideoObject') {
            $matches = [];
            if (preg_match('/https:\/\/www.twitch.tv\/videos\/(.*)/',$item['url'], $matches)) {
              $arr = ['name' => $item['name'],
              'description' => $item['description'],
              'uploadDate' => DateHelper::normalizeDate($item['uploadDate']),
              'url' => $item['url']];
              if (is_null($matches[1])) {
                throw new \LogicException('Нельзя добавить запись без twitchId');
              }
              $arr['twitch_id'] = $matches[1];
              if (!is_null($streamerId)) {
                $arr['streamer_id'] = $streamerId;
              }
              $collection[] = $arr;        
            }
          }
        }
        return $collection;
      }
}