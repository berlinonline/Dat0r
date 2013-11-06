<?php

namespace Dat0r\Tests\Type\Collection\Fixtures;

use Dat0r\Type\Collection\TypedMap;

class TestObjectMap extends TypedMap
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Tests\\Fixtures\\TestObject';
    }
}
