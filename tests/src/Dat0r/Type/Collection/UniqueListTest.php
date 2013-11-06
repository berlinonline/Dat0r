<?php

namespace Dat0r\Tests\Type\Collection;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Tests\Type\Collection\Fixtures\UniqueTestObjectList;

use Faker;

class UniqueListTest extends TestCase
{
    /**
     * @expectedException Dat0r\Type\Collection\Exception
     */
    public function testUniqueness()
    {
        $items = TestObject::createRandomInstances();

        $list = new UniqueTestObjectList($items);
        $list->addItem($items[0]);
    }
}
