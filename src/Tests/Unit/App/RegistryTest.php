<?php
namespace TwitchWatcher\Tests\Unit\App;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    private Registry $reg;
    public function setUp(): void
    {
        $config = $this->createMock(Config::class);
        $configStub = $this->createStub(Config::class);
        $configStub->method();
        $this->registry = new Registry();
    }
}