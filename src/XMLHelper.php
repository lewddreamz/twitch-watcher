<?php

declare(strict_types=1);

namespace TwitchWatcher;

class XMLHelper
{
    public static function getLDJSON(string $html):array|false
    {
        $xml = new \DOMDocument;
        @$xml->loadHTML($html);
        $xpath = new \DOMXpath($xml);
        $xmlNodes = $xpath->query("//script[@type='application/ld+json']");
        $result = [];
        foreach ($xmlNodes as $xmlNode) {
            $jsonArr = json_decode($xmlNode->textContent, true);
            $result[] = $jsonArr;
        }
        return $result;
    }
}