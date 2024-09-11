<?php
namespace TwitchWatcher\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TwitchWatcher\Models\ModelInterface;
use TwitchWatcher\Models\Streamer;

class TestIteratorCollectionTest extends TestCase
{

    public $collection;
    public $foo;
    public array $stubs;
    
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->createStubs();
    }
    public function setUp(): void
    {
        $this->collection = new TestIteratorCollection();
        $this->collection->setType(ModelInterface::class);
        foreach ($this->stubs as $stub) {
            $this->collection->add($stub);
        }
        $this->collection->rewind();
    }

    private function createStubs(): void
    {
        $stubs = [];
        for ($i = 0; $i < 10; $i++)
        {
            $stubs[$i] = $this->createStub(ModelInterface::class);
            $stubs[$i]->id = $i;
        }
        $this->stubs = $stubs;
    }

    /* Это перенести в тест для AbstractCollection
    public function testAddWrongType()
    {
        $this->collection->allowChildren = false;
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
*/
    public function testCurrent()
    {
        $this->collection->setType(ModelInterface::class);
        $this->collection->add($this->stubs[0]);
        $this->collection->add($stub2 = $this->createStub(ModelInterface::class));
        $this->collection->add($stub3 = $this->createStub(ModelInterface::class));
        $this->assertSame($this->collection->current(), $this->stubs[0]);
    }
/*TODO
public function testAllowChildrenFalse()
public function testAllowChildrenTrue()
*/

    public function testKey()
    {
        $this->assertEquals(0, $this->collection->key());
        foreach($this->collection as $el) {
            is_null($el);
        }
        $this->assertEquals(10, $this->collection->key());
    }

    public function testNext()
    {
        $this->assertEquals(0, $this->collection->key());
        $this->collection->next();
        $this->assertEquals(1, $this->collection->key());
        for ($i = 1; $i < 10; $i++) {
            $this->collection->next();
        }
        $this->assertEquals(10, $this->collection->key());
    }
    public function testRewind()
    {
        foreach($this->collection as $el) {
            is_null($el);
        }
        $this->assertEquals(10, $this->collection->key());
        $this->collection->rewind();
        $this->assertEquals(0, $this->collection->key());
    }
    public function testValid()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue($this->collection->valid());
            $this->collection->next();
        }
        $this->assertFalse($this->collection->valid());        
    }
}