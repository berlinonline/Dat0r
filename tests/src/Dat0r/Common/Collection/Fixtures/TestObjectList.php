<?php

namespace Dat0r\Tests\Common\Collection\Fixtures;

use Dat0r\Common\Collection\TypedList;

class TestObjectList extends TypedList
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Tests\\Common\\Fixtures\\TestObject';
    }
}
