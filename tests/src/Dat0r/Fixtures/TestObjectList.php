<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r\TypedList;

class TestObjectList extends TypedList
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Tests\\Fixtures\\TestObject';
    }
}
