<?php

namespace Dat0r\Tests\Runtime\Attribute\Value\Type;

use Mockery;
use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Runtime\Attribute\Value\Type\AggregateCollectionValue;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Entity\EntityList;

class AggregateCollectionValueTest extends TestCase
{
    public function testCreate()
    {
        $value = new AggregateCollectionValue(
            new AggregateCollection(
                'content_objects',
                array(
                    'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Fixtures\\ParagraphType'),
                )
            )
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Value\\Type\\AggregateCollectionValue',
            $value
        );
    }

    public function testDefaultValue()
    {
        $aggregate_attribute = new AggregateCollection(
            'content_objects',
            array(
                'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Fixtures\\ParagraphType'),
            )
        );

        $value = $aggregate_attribute->createValue();

        $entity_list = $value->get();
        $this->assertInstanceOf('Dat0r\\Runtime\\Entity\\EntityList', $entity_list);
        $this->assertEquals(0, $entity_list->getSize());
    }

    public function testValueChangedEvents()
    {
        $listener = Mockery::mock('\Dat0r\Runtime\Attribute\Value\ValueChangedListenerInterface');
        $listener->shouldReceive('onValueChanged')->with(
            '\Dat0r\Runtime\Attribute\Value\ValueChangedEvent'
        )->twice();

        $aggregate_type = new ParagraphType();
        $aggregated_entity = $aggregate_type->createEntity(
            array('title' => 'Hello world', 'content' => 'Foobar lorem ipsum...')
        );

        $aggregate_attribute = new AggregateCollection(
            'content_objects',
            array(
                'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Fixtures\\ParagraphType'),
            )
        );

        $value = $aggregate_attribute->createValue();
        $value->addValueChangedListener($listener);

        $entity_list = $value->get();
        $entity_list->push($aggregated_entity);

        $aggregated_entity->setValue('title', 'Kthxbye');

        $this->assertInstanceOf('Dat0r\\Runtime\\Entity\\EntityList', $entity_list);
        $this->assertEquals(1, $entity_list->getSize());
    }
}
