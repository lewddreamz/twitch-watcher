<?php
declare(strict_types=1);

namespace TwitchWatcher\Tests\Unit\Services;
use PHPUnit\Framework\TestCase;
use TwitchWatcher\Services\Http;

class HttpServiceTest extends TestCase
{
    public Http $http;
    public function setUp(): void
    {
        $this->http = new Http();
    }
    public function testGet()
    {
        $response = $this->http->get("https://www.twitch.tv/venomvelind/videos?filter=archives&sort=time");
        dump($response->getContent());
    }
}


$h = new HttpServiceTest('name') ;
$h->testGet();
