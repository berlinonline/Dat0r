<?php

namespace Dat0r\Tests\Common\Collection;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Common\Fixtures\TestObject;
use Dat0r\Tests\Common\Collection\Fixtures\TestObjectMap;
use Dat0r\Tests\Common\Collection\Fixtures\UnsupportedObject;

use Faker;

class TypedMapTest extends TestCase
{
    public function testCreate()
    {
        $items = $this->createRandomItems();
        $map = new TestObjectMap($items);

        $this->assertInstanceOf('\\Dat0r\\Common\\Collection\\CollectionInterface', $map);
        $this->assertInstanceOf('\\Dat0r\\Common\\Collection\\MapInterface', $map);
        $this->assertInstanceOf('\\Dat0r\\Common\\Collection\\TypedMap', $map);
        $this->assertEquals(count($items), $map->getSize());
    }

    public function testSetItem()
    {
        $items = $this->createRandomItems();

        $map = new TestObjectMap();
        foreach ($items as $key => $item) {
            $map->setItem($key, $item);
        }

        // assert item count
        $expected_item_count = count($items);
        $this->assertEquals($expected_item_count, count($map));

        // assert item keys
        foreach ($map as $key => $item) {
            $expected_item = $items[$key];
            $this->assertEquals($expected_item, $item);
        }
    }

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testSetInvalidItem()
    {
        $map = new TestObjectMap();
        $map->setItem("foobar", new UnsupportedObject());
    }

    protected function createRandomItems()
    {
        $items = [];
        $faker = Faker\Factory::create();
        foreach (TestObject::createRandomInstances() as $item) {
            $items[$faker->word(12)] = $item;
        }

        return $items;
    }
}
