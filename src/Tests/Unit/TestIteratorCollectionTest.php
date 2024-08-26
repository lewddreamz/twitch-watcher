<?php
namespace TwitchWatcher\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TwitchWatcher\Models\Streamer;

class TestIteratorCollectionTest extends TestCase
{

    public $collection;
    public $foo;
    public function setUp(): void
    {
        $this->collection = new TestIteratorCollection();
        $this->foo = new Foo();
    }
    public function testCollectionHasPropertyType()
    {
        $this->assertObjectHasProperty('type', $this->collection);
    }

    public function testAddWrongType()
    {
        $this->collection->setType(Foo::class);
        $this->expectException(InvalidArgumentException::class);
        $this->collection->add(new Streamer);
    }
    public function testAddStreamer() {
        $this->collection->setType(Streamer::class);
        $streamer = new Streamer();
        $this->collection->add($streamer);
        $this->assertIsObject($this->collection->current());
        $this->assertTrue($this->collection->current() instanceof (Streamer::class));
    }
}