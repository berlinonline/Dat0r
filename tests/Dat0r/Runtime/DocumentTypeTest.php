<?php

namespace Dat0r\Tests\Runtime;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Tests\Runtime\Fixtures\InvalidType;
use Dat0r\Runtime\IDocumentType;

class DocumentTypeTest extends TestCase
{
    public function testCreateArticleType()
    {
        $type = new ArticleType();

        $this->assertEquals('Article', $type->getName());
        $this->assertEquals(12, $type->getAttributes()->getSize());
    }

    public function testCreateAggregateType()
    {
        $type = new ParagraphType();

        $this->assertEquals(2, $type->getAttributes()->getSize());
        $this->assertEquals('Paragraph', $type->getName());
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributeMethod(IDocumentType $type)
    {
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $type->getAttribute('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Number', $type->getAttribute('click_count'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributesMethodPlain(IDocumentType $type)
    {
        $attributes = $type->getAttributes();

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\AttributeMap',
            $attributes
        );

        $this->assertEquals(12, $attributes->getSize());

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\Text',
            $attributes->getItem('headline')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\Text',
            $attributes->getItem('content')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\Number',
            $attributes->getItem('click_count')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\Text',
            $attributes->getItem('author')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\Text',
            $attributes->getItem('email')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\TextCollection',
            $attributes->getItem('keywords')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\Boolean',
            $attributes->getItem('enabled')
        );
        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\NumberCollection',
            $attributes->getItem('images')
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\KeyValue',
            $attributes->getItem('meta')
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\AggregateCollection',
            $attributes->getItem('content_objects')
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\Type\\ReferenceCollection',
            $attributes->getItem('references')
        );
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testGetAttributesMethodFiltered(IDocumentType $type)
    {
        $attributes = $type->getAttributes(array('headline', 'click_count'));

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\AttributeMap', $attributes);
        $this->assertEquals(2, $attributes->getSize());

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $attributes->getItem('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Number', $attributes->getItem('click_count'));
    }

    /**
     * @dataProvider provideTypeInstances
     */
    public function testCreateDocument(IDocumentType $type)
    {
        $document = $type->createDocument();
        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\Document', $document);
    }

    /**
     * @dataProvider provideTypeInstances
     * @expectedException Dat0r\Common\Error\RuntimeException
     */
    public function testInvalidAttributeException(IDocumentType $type)
    {
        $type->getAttribute('foobar-attribute-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testInvalidDocumentImplementorException()
    {
        $type = new InvalidType();
        $type->createDocument(); // @codeCoverageIgnoreStart
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
