<?php
declare(strict_types=1);
namespace TwitchWatcher\Tests\Unit\Models;
use DeepCopy\Exception\PropertyException;
use PHPUnit\Framework\TestCase;
class ModelTest extends TestCase
{
    public TestModel $model;

    public function setUp(): void
    {
        $this->model = new TestModel;
    }
    public function testFillAllProps()
    {
        $props = [
            'stringProp' => 'str',
            'intProp'    => 1,
            'nonExistentProp' => 'something'
        ];
        $this->model->fill($props);

        $this->assertEquals('str', $this->model->stringProp);
        $this->assertEquals(1, $this->model->intProp);
        $this->expectException(PropertyException::class);
        $this->model->nonExistentProp;
    }

    public function testFillWithWrongTypes()
    {
        $props = [
            'stringProp' => 123,
            'intProp'    => 'asdf',
        ];
        $this->expectException(\Throwable::class);
        try {
            $this->model->fill($props);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function testGetValues()
    {
        $model = new TestModel();
        //Инициализируем одно свойство
        $model->intProp = 1;
        $arr = $model->getValues();
        $this->assertSame(1, $arr['intProp'], "test");
        $this->assertArrayHasKey('stringProp', $arr, "HasKeyAssertion");
        $this->assertNull($arr['stringProp'], "Prop is null assertion");
    }
}