<?php

namespace Dat0r\Tests\Type\Collection;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Tests\Type\Collection\Fixtures\TestObjectMap;
use Dat0r\Tests\Type\Collection\Fixtures\UnsupportedObject;

use Faker;

class TypedMapTest extends TestCase
{
    public function testCreate()
    {
        $items = $this->createRandomItems();
        $map = new TestObjectMap($items);

        $this->assertInstanceOf('\\Dat0r\\Type\\Collection\\ICollection', $map);
        $this->assertInstanceOf('\\Dat0r\\Type\\Collection\\IMap', $map);
        $this->assertInstanceOf('\\Dat0r\\Type\\Collection\\TypedMap', $map);
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
     * @expectedException Dat0r\Type\Collection\Exception
     */
    public function testSetInvalidItem()
    {
        $map = new TestObjectMap();
        $map->setItem("foobar", new UnsupportedObject());
    }

    protected function createRandomItems()
    {
        $items = array();
        $faker = Faker\Factory::create();
        foreach (TestObject::createRandomInstances() as $item) {
            $items[$faker->word(12)] = $item;
        }

        return $items;
    }
}
