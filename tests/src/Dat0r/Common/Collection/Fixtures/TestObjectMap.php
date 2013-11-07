<?php

namespace Dat0r\Tests\Common\Collection\Fixtures;

use Dat0r\Common\Collection\TypedMap;

class TestObjectMap extends TypedMap
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Tests\\Common\\Fixtures\\TestObject';
    }
}
