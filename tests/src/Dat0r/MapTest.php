<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Map;

use Faker;

class MapTest extends TestCase
{
    public function testCreate()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $this->assertInstanceOf('\\Dat0r\\ICollection', $map);
        $this->assertInstanceOf('\\Dat0r\\IMap', $map);
    }

    public function testGetItem()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);
        $keys = array_keys($items);

        $item = $map->getItem($keys[0]);
        $this->assertEquals($items[$keys[0]], $item);
    }

    public function testGetItems()
    {
        $expected_items = $this->createRandomItems();
        $map = new Map($expected_items);
        $all_keys = array_keys($expected_items);
        $item_count = count($expected_items);

        $keys = array();
        for ($i = 0; $i < $item_count && $i <= 3; $i++) {
            $keys[] = $all_keys[$i];
        }

        $actual_items = $map->getItems($keys);
        $this->assertEquals(count($keys), count($actual_items));

        foreach ($keys as $idx => $key) {
            $item = $map->getItem($key);
            $this->assertEquals($item, $actual_items[$idx]);
        }
    }

    public function testSetItem()
    {
        $map = new Map($this->createRandomItems());
        $start_size = $map->getSize();

        $faker = Faker\Factory::create();
        $key = $faker->word(17);

        $item = TestObject::createRandomInstances();
        $map->setItem($key, $item);

        $this->assertEquals($item, $map->getItem($key));
        $this->assertEquals($start_size + 1, $map->getSize());
    }

    public function testSetItems()
    {
        $start_items = $this->createRandomItems();
        $map = new Map($start_items);
        $start_size = $map->getSize();

        $new_items = $this->createRandomItems();
        $map->setItems($new_items);

        $new_keys = array_diff(array_keys($new_items), array_keys($start_items));
        $this->assertEquals($start_size + count($new_keys), $map->getSize());
    }

    public function testRemoveItem()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);
        $start_size = $map->getSize();

        $keys = array_keys($items);
        $first_key = $keys[0];
        $map->removeItem($items[$first_key]);

        $this->assertEquals($start_size - 1, $map->getSize());
    }

    public function testRemoveItems()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $map->removeItems(array_values($items));

        $this->assertEquals(0, $map->getSize());
    }

    public function testGetKeys()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $this->assertEquals(array_keys($items), $map->getKeys());
    }

    public function testGetValues()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $this->assertEquals(array_values($items), $map->getValues());
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
