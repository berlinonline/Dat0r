<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures;

class ArrayListTest extends CollectionTestCase
{
    protected function createListInstance(array $items = array())
    {
        return Fixtures\TestObjectList::create($items);
    }

    protected function createRandomListItems()
    {
        return Fixtures\TestObject::createRandomInstances();
    }
}
