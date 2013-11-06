<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures\TestObject;
use Dat0r\Tests\Fixtures\UniqueTestObjectList;

use Faker;

class UniqueListTest extends TestCase
{
    /**
     * @expectedException Dat0r\Exception
     */
    public function testUniqueness()
    {
        $items = TestObject::createRandomInstances();

        $list = new UniqueTestObjectList($items);
        $list->addItem($items[0]);
    }
}
