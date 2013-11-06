<?php

namespace Dat0r\Tests\Type\Collection;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Tests\Type\Collection\Fixtures\TestObjectList;
use Dat0r\Tests\Type\Collection\Fixtures\UnsupportedObject;

use Faker;

class TypedListTest extends TestCase
{
    public function testCreate()
    {
        $items = TestObject::createRandomInstances();
        $list = new TestObjectList($items);

        $this->assertInstanceOf('\\Dat0r\\Type\\Collection\\ICollection', $list);
        $this->assertInstanceOf('\\Dat0r\\Type\\Collection\\IList', $list);
        $this->assertInstanceOf('\\Dat0r\\Type\\Collection\\TypedList', $list);
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
     * @expectedException Dat0r\Type\Collection\Exception
     */
    public function testAddInvalidScalar()
    {
        $list = new TestObjectList();
        $list->addItem("foobar");
    }

    /**
     * @expectedException Dat0r\Type\Collection\Exception
     */
    public function testAddInvalidObject()
    {
        $list = new TestObjectList();
        $list->addItem(new UnsupportedObject());
    }
}
