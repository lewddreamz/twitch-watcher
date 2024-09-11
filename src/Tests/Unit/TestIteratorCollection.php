<?php
namespace TwitchWatcher\Tests\Unit;
use TwitchWatcher\Collections\AbstractIteratorCollection;
class TestIteratorCollection extends AbstractIteratorCollection
{
    public bool $allowChildren = true;
     public function setType(string $type)
     {
        if (!class_exists($type) && !interface_exists($type)) {
            throw new \InvalidArgumentException("There is no class " . $type);
        }
        $this->type = $type;
     }
}