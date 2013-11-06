<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Tests\Fixtures\UniqueTestObjectMap;

use Faker;

class UniqueMapTest extends TestCase
{
    /**
     * @expectedException Dat0r\Exception
     */
    public function testUniqueness()
    {
        $items = $this->createRandomItems();

        $map = new UniqueTestObjectMap($items);
        $keys = $map->getKeys();
        $map->setItem('foobar', $items[$keys[0]]);
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
