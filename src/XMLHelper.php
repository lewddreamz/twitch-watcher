<?php

declare(strict_types=1);

namespace TwitchWatcher;

class XMLHelper
{
    public static function getLDJSON(string $html)
    {
        $xml = new \DOMDocument;
        @$xml->loadHTML($html);
        $xpath = new \DOMXpath($xml);
        $xmlNodes = $xpath->query("//script[@type='application/ld+json']");
        foreach ($xmlNodes as $xmlNode) {
            $jsonArr = json_decode($xmlNode->textContent, true);
            return $jsonArr;
        }
    }
}