<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r\TypedMap;

class TestObjectMap extends TypedMap
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Tests\\Fixtures\\TestObject';
    }
}
