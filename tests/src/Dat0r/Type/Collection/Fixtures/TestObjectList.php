<?php

namespace Dat0r\Tests\Type\Collection\Fixtures;

use Dat0r\Type\Collection\TypedList;

class TestObjectList extends TypedList
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Tests\\Fixtures\\TestObject';
    }
}
