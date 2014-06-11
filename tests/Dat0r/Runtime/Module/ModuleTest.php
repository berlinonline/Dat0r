<?php

namespace Dat0r\Tests\Runtime\Module;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Module\Fixtures\ArticleModule;
use Dat0r\Tests\Runtime\Module\Fixtures\ParagraphModule;
use Dat0r\Tests\Runtime\Module\Fixtures\InvalidModule;
use Dat0r\Runtime\Module\IModule;

class ModuleTest extends TestCase
{
    public function testCreateArticleModule()
    {
        $module = ArticleModule::getInstance();

        $this->assertEquals('Article', $module->getName());
        $this->assertEquals(11, $module->getAttributes()->getSize());
    }

    public function testCreateAggegateModule()
    {
        $module = new ParagraphModule();

        $this->assertEquals(2, $module->getAttributes()->getSize());
        $this->assertEquals('Paragraph', $module->getName());
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetAttributeMethod(IModule $module)
    {
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $module->getAttribute('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Number', $module->getAttribute('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetAttributesMethodPlain(IModule $module)
    {
        $attributes = $module->getAttributes();

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\AttributeMap', $attributes);
        $this->assertEquals(11, $attributes->getSize());

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $attributes->getItem('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $attributes->getItem('content'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Number', $attributes->getItem('clickCount'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $attributes->getItem('author'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $attributes->getItem('email'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\TextCollection', $attributes->getItem('keywords'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Boolean', $attributes->getItem('enabled'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\NumberCollection', $attributes->getItem('images'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\KeyValue', $attributes->getItem('meta'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\AggregateCollection', $attributes->getItem('paragraph'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\ReferenceCollection', $attributes->getItem('references'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetAttributesMethodFiltered(IModule $module)
    {
        $attributes = $module->getAttributes(array('headline', 'clickCount'));

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\AttributeMap', $attributes);
        $this->assertEquals(2, $attributes->getSize());

        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Text', $attributes->getItem('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Type\\Number', $attributes->getItem('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testCreateDocument(IModule $module)
    {
        $document = $module->createDocument();
        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\Document', $document);
    }

    /**
     * @dataProvider provideModuleInstances
     * @expectedException Dat0r\Common\Error\RuntimeException
     */
    public function testInvalidAttributeException(IModule $module)
    {
        $module->getAttribute('foobar-attribute-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testInvalidDocumentImplementorException()
    {
        $module = InvalidModule::getInstance();
        $module->createDocument(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @codeCoverageIgnore
     */
    public static function provideModuleInstances()
    {
        return array(array(ArticleModule::getInstance()));
    }

    public function testGetAttributeByPath()
    {
        $module = ArticleModule::getInstance();
        $attribute = $module->getAttribute('paragraph.paragraph.title');

        $this->assertEquals('title', $attribute->getName());
    }
}
