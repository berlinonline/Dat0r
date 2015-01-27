<?php

namespace Dat0r\Tests\Runtime\Attribute\Value\Type;

use Mockery;
use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListValueHolder;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\ValueHolder\ValueChangedEvent;
use Dat0r\Runtime\ValueHolder\ValueChangedListenerInterface;

class EmbeddedEntityListValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $value = new EmbeddedEntityListValueHolder(
            new EmbeddedEntityListAttribute(
                'content_objects',
                array(
                    EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS),
                )
            )
        );

        $this->assertInstanceOf(EmbeddedEntityListValueHolder::CLASS, $value);
    }

    public function testDefaultValue()
    {
        $aggregate_attribute = new EmbeddedEntityListAttribute(
            'content_objects',
            array(
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS),
            )
        );

        $value = $aggregate_attribute->createValue();

        $entity_list = $value->get();
        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $this->assertEquals(0, $entity_list->getSize());
    }

    public function testValueChangedEvents()
    {
        $listener = Mockery::mock(ValueChangedListenerInterface::CLASS);
        $listener->shouldReceive('onValueChanged')->with(ValueChangedEvent::CLASS)->twice();

        $aggregate_type = new ParagraphType();
        $aggregated_entity = $aggregate_type->createEntity(
            array('title' => 'Hello world', 'content' => 'Foobar lorem ipsum...')
        );

        $aggregate_attribute = new EmbeddedEntityListAttribute(
            'content_objects',
            array(
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS),
            )
        );

        $value = $aggregate_attribute->createValue();
        $value->addValueChangedListener($listener);

        $entity_list = $value->get();
        $entity_list->push($aggregated_entity);

        $aggregated_entity->setValue('title', 'Kthxbye');

        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $this->assertEquals(1, $entity_list->getSize());
    }
}