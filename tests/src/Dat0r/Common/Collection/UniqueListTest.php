<?php

namespace Dat0r\Tests\Common\Collection;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Common\Fixtures\TestObject;
use Dat0r\Tests\Common\Collection\Fixtures\UniqueTestObjectList;

use Faker;

class UniqueListTest extends TestCase
{
    /**
     * @expectedException Dat0r\Common\Collection\Exception
     */
    public function testUniqueness()
    {
        $items = TestObject::createRandomInstances();

        $list = new UniqueTestObjectList($items);
        $list->addItem($items[0]);
    }
}
