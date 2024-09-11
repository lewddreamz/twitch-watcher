<?php
declare(strict_types=1);
namespace TwitchWatcher\Tests\Unit\Models;
use TwitchWatcher\Models\AbstractModel;
class TestModel extends AbstractModel
{
    protected array $attributes = [
        'stringProp', 'intProp'
    ];

    public string $stringProp;
    public int $intProp;
}