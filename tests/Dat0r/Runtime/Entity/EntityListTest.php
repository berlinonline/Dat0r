<?php

namespace Dat0r\Tests\Runtime\Entity;

use Dat0r\Common\Collection\ArrayList;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Tests\Runtime\Entity\Fixtures\EntityTestProxy;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = new EntityList();

        $this->assertInstanceOf(EntityList::CLASS, $collection);
    }

    public function testAddEntityToEmptyCollection()
    {
        $type = new ArticleType();
        $collection = new EntityList();

        $test_entity = $type->createEntity();
        $collection->addItem($test_entity);

        $first_entity = $collection->getFirst();
        $this->assertEquals($test_entity, $first_entity);
    }

    public function testAddEntityToNonEmptyCollection()
    {
        $type = new ArticleType();
        $test_entity = $type->createEntity();

        $collection = new EntityList([ $test_entity ]);

        $collection->addItem($test_entity);

        $first_entity = $collection[0];
        $second_entity = $collection[1];

        $this->assertEquals($test_entity, $first_entity);
        $this->assertEquals($test_entity, $second_entity);
    }
}
