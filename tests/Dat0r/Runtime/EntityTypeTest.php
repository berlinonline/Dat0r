<?php

namespace Dat0r\Tests\Runtime;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Tests\Runtime\Fixtures\InvalidType;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\Entity\EntityInterface;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\TextList\TextListAttribute;
use Dat0r\Runtime\Attribute\Boolean\BooleanAttribute;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Attribute\Number\NumberAttribute;
use Dat0r\Runtime\Attribute\NumberList\NumberListAttribute;
use Dat0r\Runtime\Attribute\KeyValue\KeyValueAttribute;

class EntityTypeTest extends TestCase
{
    public function testCreateArticleType()
    {
        $type = new ArticleType();

        $this->assertEquals('Article', $type->getName());
        $this->assertEquals(11, $type->getAttributes()->getSize());
    }

    public function testAccessNestedParameters()
    {
        $type = new ArticleType();

        $this->assertEquals('bar', $type->getOption('foo'));
        $this->assertEquals('blub', $type->getOption('nested')->get('blah'));
    }

    public function testCreateEmbedType()
    {
        $type = new ParagraphType();

        $this->assertEquals(2, $type->getAttributes()->getSize());
        $this->assertEquals('Paragraph', $type->getName());
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributeMethod(EntityTypeInterface $type)
    {
        $this->assertInstanceOf(TextAttribute::CLASS, $type->getAttribute('headline'));
        $this->assertInstanceOf(NumberAttribute::CLASS, $type->getAttribute('click_count'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributesMethodPlain(EntityTypeInterface $type)
    {
        $attributes = $type->getAttributes();

        $this->assertInstanceOf(AttributeMap::CLASS, $attributes);

        $this->assertEquals(11, $attributes->getSize());

        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('headline'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('content'));
        $this->assertInstanceOf(NumberAttribute::CLASS, $attributes->getItem('click_count'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('author'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('email'));
        $this->assertInstanceOf(TextListAttribute::CLASS, $attributes->getItem('keywords'));
        $this->assertInstanceOf(BooleanAttribute::CLASS, $attributes->getItem('enabled'));
        $this->assertInstanceOf(NumberListAttribute::CLASS, $attributes->getItem('images'));
        $this->assertInstanceOf(KeyValueAttribute::CLASS, $attributes->getItem('meta'));
        $this->assertInstanceOf(EmbeddedEntityListAttribute::CLASS, $attributes->getItem('content_objects'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributesMethodFiltered(EntityTypeInterface $type)
    {
        $attributes = $type->getAttributes(array('headline', 'click_count'));

        $this->assertInstanceOf(AttributeMap::CLASS, $attributes);
        $this->assertEquals(2, $attributes->getSize());

        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('headline'));
        $this->assertInstanceOf(NumberAttribute::CLASS, $attributes->getItem('click_count'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testCreateEntity(EntityTypeInterface $type)
    {
        $entity = $type->createEntity();
        $this->assertInstanceOf(EntityInterface::CLASS, $entity);
    }

    /**
     * @dataProvider provideTypeInstances
     * @expectedException Dat0r\Common\Error\RuntimeException
     */
    public function testInvalidAttributeException(EntityTypeInterface $type)
    {
        $type->getAttribute('foobar-attribute-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testInvalidEntityImplementorException()
    {
        $type = new InvalidType();
        $type->createEntity(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @codeCoverageIgnore
     */
    public static function provideTypeInstances()
    {
        return array(array(new ArticleType()));
    }

    public function testGetAttributeByPath()
    {
        $type = new ArticleType();
        $attribute = $type->getAttribute('content_objects.paragraph.title');

        $this->assertEquals('title', $attribute->getName());
    }
}
