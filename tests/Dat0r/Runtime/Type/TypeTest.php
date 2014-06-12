<?php

namespace Dat0r\Tests\Runtime\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Type\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Type\Fixtures\ParagraphType;
use Dat0r\Tests\Runtime\Type\Fixtures\InvalidType;
use Dat0r\Runtime\Type\IType;

class TypeTest extends TestCase
{
    public function testCreateArticleType()
    {
        $type = ArticleType::getInstance();

        $this->assertEquals('Article', $type->getName());
        $this->assertEquals(11, $type->getAttributes()->getSize());
    }

    public function testCreateAggegateType()
    {
        $type = new ParagraphType();

        $this->assertEquals(2, $type->getAttributes()->getSize());
        $this->assertEquals('Paragraph', $type->getName());
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributeMethod(IType $type)
    {
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Bundle\\Text', $type->getAttribute('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Bundle\\Number', $type->getAttribute('clickCount'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributesMethodPlain(IType $type)
    {
        $attributes = $type->getAttributes();

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\AttributeMap',
            $attributes
        );

        $this->assertEquals(11, $attributes->getSize());

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\Text',
            $attributes->getItem('headline')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\Text',
            $attributes->getItem('content')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\Number',
            $attributes->getItem('clickCount')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\Text',
            $attributes->getItem('author')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\Text',
            $attributes->getItem('email')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\TextCollection',
            $attributes->getItem('keywords')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\Boolean',
            $attributes->getItem('enabled')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\NumberCollection',
            $attributes->getItem('images')
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\KeyValue',
            $attributes->getItem('meta')
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\AggregateCollection',
            $attributes->getItem('paragraph')
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Bundle\\ReferenceCollection',
            $attributes->getItem('references')
        );
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributesMethodFiltered(IType $type)
    {
        $attributes = $type->getAttributes(array('headline', 'clickCount'));

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\AttributeMap', $attributes);
        $this->assertEquals(2, $attributes->getSize());

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Bundle\\Text', $attributes->getItem('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Bundle\\Number', $attributes->getItem('clickCount'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testCreateDocument(IType $type)
    {
        $document = $type->createDocument();
        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\Document', $document);
    }

    /**
     * @dataProvider provideTypeInstances
     * @expectedException Dat0r\Common\Error\RuntimeException
     */
    public function testInvalidAttributeException(IType $type)
    {
        $type->getAttribute('foobar-attribute-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testInvalidDocumentImplementorException()
    {
        $type = InvalidType::getInstance();
        $type->createDocument(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @codeCoverageIgnore
     */
    public static function provideTypeInstances()
    {
        return array(array(ArticleType::getInstance()));
    }

    public function testGetAttributeByPath()
    {
        $type = ArticleType::getInstance();
        $attribute = $type->getAttribute('paragraph.paragraph.title');

        $this->assertEquals('title', $attribute->getName());
    }
}
