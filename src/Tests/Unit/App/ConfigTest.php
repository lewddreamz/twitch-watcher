<?php
namespace TwitchWatcher\Tests\Unit\App;

use PHPUnit\Framework\TestCase;
use TwitchWatcher\App\Config;

class ConfigTest extends TestCase
{
    public Config $config;
    public function setUp(): void
    {
        $options = [
            'test' => 'a'
        ];
        $this->config = new Config($options);
    }
    public function testGetOption()
    {
        $test = $this->config->test;
        $this->assertEquals('a', $test);
    }
}