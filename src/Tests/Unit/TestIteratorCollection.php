<?php
namespace TwitchWatcher\Tests\Unit;
use TwitchWatcher\Collections\AbstractIteratorCollection;
class TestIteratorCollection extends AbstractIteratorCollection
{
     public function setType(string $type)
     {
        if (!class_exists($type)) {
            throw new \InvalidArgumentException("There is no class " . $type);
        }
        $this->type = $type;
     }
}