<?php

namespace Dat0r\Tests\Runtime;

use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Attribute\Boolean\BooleanAttribute;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListAttribute;
use Dat0r\Runtime\Attribute\IntegerList\IntegerListAttribute;
use Dat0r\Runtime\Attribute\Integer\IntegerAttribute;
use Dat0r\Runtime\Attribute\TextList\TextListAttribute;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Timestamp\TimestampAttribute;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\Entity\EntityInterface;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\InvalidType;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Tests\TestCase;

class EntityTypeTest extends TestCase
{
    public function testCreateArticleType()
    {
        $article_type = new ArticleType();

        $this->assertEquals('Article', $article_type->getName());
        $this->assertEquals(14, $article_type->getAttributes()->getSize());
    }

    public function testAccessNestedParameters()
    {
        $article_type = new ArticleType();

        $this->assertEquals('bar', $article_type->getOption('foo'));
        $this->assertEquals('blub', $article_type->getOption('nested')->get('blah'));
    }

    public function testCreateEmbedType()
    {
        $article_type = new ArticleType();
        $paragraph_type = new ParagraphType($article_type);

        $this->assertEquals(2, $paragraph_type->getAttributes()->getSize());
        $this->assertEquals('Paragraph', $paragraph_type->getName());
    }

    public function testGetAttributeMethod()
    {
        $article_type = new ArticleType();

        $this->assertInstanceOf(TextAttribute::CLASS, $article_type->getAttribute('headline'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $article_type->getAttribute('click_count'));
    }

    public function testGetAttributesMethodPlain()
    {
        $article_type = new ArticleType();
        $attributes = $article_type->getAttributes();

        $this->assertInstanceOf(AttributeMap::CLASS, $attributes);
        $this->assertEquals(14, $attributes->getSize());
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('headline'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('content'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $attributes->getItem('click_count'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('author'));
        $this->assertInstanceOf(TimestampAttribute::CLASS, $attributes->getItem('birthday'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('email'));
        $this->assertInstanceOf(TextListAttribute::CLASS, $attributes->getItem('keywords'));
        $this->assertInstanceOf(BooleanAttribute::CLASS, $attributes->getItem('enabled'));
        $this->assertInstanceOf(IntegerListAttribute::CLASS, $attributes->getItem('images'));
        $this->assertInstanceOf(KeyValueListAttribute::CLASS, $attributes->getItem('meta'));
        $this->assertInstanceOf(EmbeddedEntityListAttribute::CLASS, $attributes->getItem('content_objects'));
    }

    public function testGetAttributesMethodFiltered()
    {
        $article_type = new ArticleType();
        $attributes = $article_type->getAttributes([ 'headline', 'click_count' ]);

        $this->assertInstanceOf(AttributeMap::CLASS, $attributes);
        $this->assertEquals(2, $attributes->getSize());

        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('headline'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $attributes->getItem('click_count'));
    }

    public function testCreateEntity()
    {
        $article_type = new ArticleType();
        $entity = $article_type->createEntity();
        $this->assertInstanceOf(EntityInterface::CLASS, $entity);
    }

    /**
     * @expectedException Dat0r\Common\Error\RuntimeException
     */
    public function testInvalidAttributeException()
    {
        $article_type = new ArticleType();
        $article_type->getAttribute('foobar-attribute-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testInvalidEntityImplementorException()
    {
        $type = new InvalidType();
        $type->createEntity(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    public function testGetAttributeByPath()
    {
        $article_type = new ArticleType();
        $attribute = $article_type->getAttribute('content_objects.paragraph.title');

        $this->assertEquals('title', $attribute->getName());
    }
}
