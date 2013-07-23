<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures;

class SetTest extends CollectionTestCase
{
    protected function createListInstance(array $items = array())
    {
        return Fixtures\TestObjectSet::create($items);
    }

    protected function createRandomListItems()
    {
        return Fixtures\TestObject::createRandomInstances();
    }

    public function testSet()
    {
        $items = array();
        foreach (Fixtures\TestObject::createRandomInstances() as $item) {
            $items[$item->getPropertyOne()] = $item;
        }

        $items = array_values($items);
        $object_set = Fixtures\TestObjectSet::create($items);

        foreach ($items as $item) {
            $object_set->add($item);
        }

        $expected_count = count($items);
        $this->assertEquals($expected_count, count($object_set));
    }
}
