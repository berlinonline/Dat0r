<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Tests\Fixtures\TestObjectList;

use Dat0r\Map;
use Faker;

class TypedListTest extends TestCase
{
    public function testCreate()
    {
        $items = TestObject::createRandomInstances();
        $list = new TestObjectList($items);

        $this->assertInstanceOf('\\Dat0r\\ICollection', $list);
        $this->assertInstanceOf('\\Dat0r\\IList', $list);
        $this->assertInstanceOf('\\Dat0r\\TypedList', $list);
        $this->assertEquals(count($items), $list->getSize());
    }

    public function testAddItem()
    {
        $items = TestObject::createRandomInstances();

        $list = new TestObjectList();
        foreach ($items as $item) {
            $list->addItem($item);
        }

        // assert item count
        $expected_item_count = count($items);
        $this->assertEquals($expected_item_count, count($list));

        // assert item order
        foreach ($list as $index => $object) {
            $expected_item = $items[$index];
            $this->assertEquals($expected_item, $object);
        }
    }

    /**
     * @expectedException Dat0r\Exception
     */
    public function testAddInvalidItem()
    {
        $list = new TestObjectList();
        $list->addItem("foobar");
    }
}
