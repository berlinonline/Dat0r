<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures;

abstract class CollectionTestCase extends TestCase
{
    abstract protected function createCollectionInstance(array $items = array());

    abstract protected function createRandomItems();

    public function testCreate()
    {
        $items = $this->createRandomItems();
        $collection = $this->createCollectionInstance($items);

        $this->assertInstanceOf('\\Dat0r\\ICollection', $collection);
    }

    public function testAdd()
    {
        $items = $this->createRandomItems();

        $collection = $this->createCollectionInstance();
        foreach ($items as $item) {
            $collection->add($item);
        }

        // assert item count
        $expected_item_count = count($items);
        $this->assertEquals($expected_item_count, count($collection));

        // assert item order
        foreach ($collection as $index => $object) {
            $expected_item = $items[$index];
            $this->assertEquals($expected_item, $object);
        }
    }

    public function testAddMore()
    {
        $initial_items = $this->createRandomItems();
        $initial_items_count = count($initial_items);

        $items = $this->createRandomItems();
        $more_items_count = count($items);

        $collection = $this->createCollectionInstance($initial_items);
        $collection->addMore($items);

        // assert item count
        $expected_item_count = $initial_items_count + $more_items_count;
        $this->assertEquals($expected_item_count, count($collection));

        // assert item order
        foreach ($collection as $index => $object) {
            $expected_item = null;
            if ($index < $initial_items_count) {
                $expected_item = $initial_items[$index];
            } else {
                $expected_item = $items[$index - $initial_items_count];
            }

            $this->assertEquals($expected_item, $object);
        }
    }

    public function testRemove()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        $random_index = 0;

        // pick a random item from the list data to be removed
        if ($last_index > 0) {
            $random_index = self::$faker->randomNumber(0, $last_index);
        }
        $random_item = $items[$random_index];

        $collection = $this->createCollectionInstance($items);
        $collection->remove($random_item);

        // assert item count
        $expected_item_count = $items_count - 1;
        $this->assertEquals($expected_item_count, count($collection));

        // assert item order
        $expected_items = array();
        foreach ($items as $index => $item) {
            if ($index !== $random_index) {
                $expected_items[] = $item;
            }
        }

        foreach ($collection as $index => $object) {
            $this->assertEquals($expected_items[$index], $object);
        }
    }

    public function testRemoveMore()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;

        // pick some random items to remove
        $max_remove = round($items_count / 2, 0);
        $numof_items_to_remove = self::$faker->randomNumber(1, $max_remove);
        $random_items = array();
        $randomly_picked_indexes = array();
        for ($i = 0; $i < $numof_items_to_remove; $i++) {
            $random_index = 0;
            do {
                if ($last_index > 0) {
                    $random_index = self::$faker->randomNumber(0, $last_index);
                }
            } while (in_array($random_index, $randomly_picked_indexes));

            $random_items[] = $items[$random_index];
            $randomly_picked_indexes[] = $random_index;
        }

        $collection = $this->createCollectionInstance($items);
        $collection->removeMore($random_items);

        // assert item count
        $expected_item_count = $items_count - count($random_items);
        $this->assertEquals($expected_item_count, count($collection));

        // assert item order
        $expected_items = array();
        foreach ($items as $index => $item) {
            if (!in_array($index, $randomly_picked_indexes)) {
                $expected_items[] = $item;
            }
        }

        foreach ($collection as $index => $object) {
            $this->assertEquals($expected_items[$index], $object);
        }
    }

    public function testGetFirst()
    {
        $items = $this->createRandomItems();
        $first_item = $items[0];

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals($first_item, $collection->getFirst());
    }

    public function testGetLast()
    {
        $items = $this->createRandomItems();
        $last_item = $items[count($items) - 1];

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals($last_item, $collection->getLast());
    }

    public function testGetOffsetFirst()
    {
        $items = $this->createRandomItems();
        $first_item = $items[0];

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals($first_item, $collection[0]);
    }

    public function testGetOffsetLast()
    {
        $items = $this->createRandomItems();
        $last_item = $items[count($items) - 1];

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals($last_item, $collection[count($items) - 1]);
    }

    public function testGetSize()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals($items_count, $collection->getSize());
    }

    public function testHasKey()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->randomNumber(0, $last_index);
        }

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals(true, $collection->hasKey($random_key));
        $this->assertEquals(false, $collection->hasKey($items_count + 1));
    }

    public function testGetKey()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->randomNumber(0, $last_index);
        }
        $random_item = $items[$random_key];

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals($random_key, $collection->getKey($random_item));
    }

    public function testHas()
    {
        $items = $this->createRandomItems();
        $other_items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->randomNumber(0, $last_index);
        }
        $random_item = $items[$random_key];

        $collection = $this->createCollectionInstance($items);
        $this->assertEquals(true, $collection->has($random_item));
        $this->assertEquals(false, $collection->has($other_items[0]));
    }

    public function testEmptyListCurrent()
    {
        $items = $this->createRandomItems();
        $empty_collection = $this->createCollectionInstance();

        $this->assertFalse($empty_collection->current());
    }

    public function testEmptyListGetFirst()
    {
        $empty_collection = $this->createCollectionInstance();

        $this->assertNull($empty_collection->getFirst());
    }

    public function testEmptyListGetLast()
    {
        $empty_collection = $this->createCollectionInstance();

        $this->assertNull($empty_collection->getLast());
    }

    /**
     * @expectedException Dat0r\Exception
     * @codeCoverageIgnore
     */
    public function testAddInvalidItem()
    {
        $collection = $this->createCollectionInstance();
        $collection->add(new Fixtures\UnsupportedObject);
    }

    /**
     * @expectedException Dat0r\Exception
     * @codeCoverageIgnore
     */
    public function testInvalidParameters()
    {
        Fixtures\ListWithInvalidParameters::create();
    }

    /**
     * @expectedException Dat0r\Exception
     * @codeCoverageIgnore
     */
    public function testEmptyParameters()
    {
        Fixtures\ListWithEmptyParameters::create();
    }
}
