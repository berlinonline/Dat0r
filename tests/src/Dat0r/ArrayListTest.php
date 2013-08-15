<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures;

class ArrayListTest extends CollectionTestCase
{
    protected function createCollectionInstance(array $items = array())
    {
        return Fixtures\TestObjectList::create($items);
    }

    protected function createRandomItems()
    {
        return Fixtures\TestObject::createRandomInstances();
    }
}
