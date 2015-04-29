<?php

namespace Dat0r\Tests\Runtime\Attribute\EmbeddedEntityList;

use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListValueHolder;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\ValueHolder\ValueChangedEvent;
use Dat0r\Runtime\ValueHolder\ValueChangedListenerInterface;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Mockery;

class EmbeddedEntityListValueHolderTest extends TestCase
{
    const ATTR_NAME = 'content_objects';

    public function testCreate()
    {
        $value = new EmbeddedEntityListValueHolder(
            new EmbeddedEntityListAttribute(
                self::ATTR_NAME,
                $this->getTypeMock(),
                [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
            )
        );

        $this->assertInstanceOf(EmbeddedEntityListValueHolder::CLASS, $value);
    }

    public function testDefaultValue()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );

        $value = $embed_attribute->createValueHolder();

        $entity_list = $value->getValue();
        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $this->assertEquals(0, $entity_list->getSize());
    }

    public function testValueChangedEvents()
    {
        $listener = Mockery::mock(ValueChangedListenerInterface::CLASS);
        $listener->shouldReceive('onValueChanged')->with(ValueChangedEvent::CLASS)->twice();

        $article_type = $this->getTypeMock();
        $embed_type = new ParagraphType($article_type, $article_type->getAttribute('content_objects'));
        $embedd_entity = $embed_type->createEntity(
            [ 'title' => 'Hello world', 'content' => 'Foobar lorem ipsum...' ]
        );

        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );

        $value = $embed_attribute->createValueHolder();
        $value->addValueChangedListener($listener);

        $entity_list = $value->getValue();
        $entity_list->push($embedd_entity);

        $embedd_entity->setValue('title', 'Kthxbye');

        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $this->assertEquals(1, $entity_list->getSize());
    }

    protected function getTypeMock($type_name = 'GenericMockType')
    {
        return new ArticleType();
    }
}