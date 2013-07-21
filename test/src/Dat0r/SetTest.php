<?php

namespace Dat0r\Tests;

class SetTest extends TestCase
{
    public function testSet()
    {
        $items = array();
        foreach (Fixtures\TestObject::createRandomInstances() as $item)
        {
            $items[$item->getPropertyOne()] = $item;
        }

        $items = array_values($items);
        $object_set = Fixtures\TestObjectSet::create($items);

        foreach ($items as $item)
        {
            $object_set->add($item);
        }

        $expected_count = count($items);
        $this->assertEquals($expected_count, count($object_set));
    }
}
