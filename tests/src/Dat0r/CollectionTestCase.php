<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures;

abstract class CollectionTestCase extends TestCase
{
    abstract protected function createListInstance(array $items = array());

    abstract protected function createRandomListItems();

    public function testCreate()
    {
        $items = $this->createRandomListItems();
        $object_list = $this->createListInstance($items);

        $this->assertInstanceOf('\\Dat0r\\ICollection', $object_list);
    }

    public function testAdd()
    {
        $items = $this->createRandomListItems();

        $object_list = $this->createListInstance();
        foreach ($items as $item) {
            $object_list->add($item);
        }

        // assert item count
        $expected_item_count = count($items);
        $this->assertEquals($expected_item_count, count($object_list));

        // assert item order
        foreach ($object_list as $index => $object) {
            $expected_item = $items[$index];
            $this->assertEquals($expected_item, $object);
        }
    }

    public function testAddMore()
    {
        $initial_items = $this->createRandomListItems();
        $initial_items_count = count($initial_items);

        $items = $this->createRandomListItems();
        $more_items_count = count($items);

        $object_list = $this->createListInstance($initial_items);
        $object_list->addMore($items);

        // assert item count
        $expected_item_count = $initial_items_count + $more_items_count;
        $this->assertEquals($expected_item_count, count($object_list));

        // assert item order
        foreach ($object_list as $index => $object) {
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
        $items = $this->createRandomListItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        $random_index = 0;

        // pick a random item from the list data to be removed
        if ($last_index > 0) {
            $random_index = self::$faker->randomNumber(0, $last_index);
        }
        $random_item = $items[$random_index];

        $object_list = $this->createListInstance($items);
        $object_list->remove($random_item);

        // assert item count
        $expected_item_count = $items_count - 1;
        $this->assertEquals($expected_item_count, count($object_list));

        // assert item order
        $expected_items = array();
        foreach ($items as $index => $item) {
            if ($index !== $random_index) {
                $expected_items[] = $item;
            }
        }

        foreach ($object_list as $index => $object) {
            $this->assertEquals($expected_items[$index], $object);
        }
    }

    public function testRemoveMore()
    {
        $items = $this->createRandomListItems();
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

        $object_list = $this->createListInstance($items);
        $object_list->removeMore($random_items);

        // assert item count
        $expected_item_count = $items_count - count($random_items);
        $this->assertEquals($expected_item_count, count($object_list));

        // assert item order
        $expected_items = array();
        foreach ($items as $index => $item) {
            if (!in_array($index, $randomly_picked_indexes)) {
                $expected_items[] = $item;
            }
        }

        foreach ($object_list as $index => $object) {
            $this->assertEquals($expected_items[$index], $object);
        }
    }

    public function testGetFirst()
    {
        $items = $this->createRandomListItems();
        $first_item = $items[0];

        $object_list = $this->createListInstance($items);
        $this->assertEquals($first_item, $object_list->getFirst());
    }

    public function testGetLast()
    {
        $items = $this->createRandomListItems();
        $last_item = $items[count($items) - 1];

        $object_list = $this->createListInstance($items);
        $this->assertEquals($last_item, $object_list->getLast());
    }

    public function testGetSize()
    {
        $items = $this->createRandomListItems();
        $items_count = count($items);

        $object_list = $this->createListInstance($items);
        $this->assertEquals($items_count, $object_list->getSize());
    }

    public function testHasKey()
    {
        $items = $this->createRandomListItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->randomNumber(0, $last_index);
        }

        $object_list = $this->createListInstance($items);
        $this->assertEquals(true, $object_list->hasKey($random_key));
        $this->assertEquals(false, $object_list->hasKey($items_count + 1));
    }

    public function testGetKey()
    {
        $items = $this->createRandomListItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->randomNumber(0, $last_index);
        }
        $random_item = $items[$random_key];

        $object_list = $this->createListInstance($items);
        $this->assertEquals($random_key, $object_list->getKey($random_item));
    }

    public function testHas()
    {
        $items = $this->createRandomListItems();
        $other_items = $this->createRandomListItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->randomNumber(0, $last_index);
        }
        $random_item = $items[$random_key];

        $object_list = $this->createListInstance($items);
        $this->assertEquals(true, $object_list->has($random_item));
        $this->assertEquals(false, $object_list->has($other_items[0]));
    }

    /**
     * @expectedException Dat0r\Exception
     */
    public function testInvalidParameters()
    {
        $corrupt_list = Fixtures\ListWithEmptyParameters::create();
    }

    public function testEmptyListCurrent()
    {
        $items = $this->createRandomListItems();
        $empty_list = $this->createListInstance();

        $this->assertFalse($empty_list->current());
    }

    public function testEmptyListGetFirst()
    {
        $empty_list = $this->createListInstance();

        $this->assertNull($empty_list->getFirst());
    }

    public function testEmptyListGetLast()
    {
        $empty_list = $this->createListInstance();

        $this->assertNull($empty_list->getLast());
    }
}
