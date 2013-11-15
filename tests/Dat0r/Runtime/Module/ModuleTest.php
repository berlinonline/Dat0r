<?php

namespace Dat0r\Tests\Runtime\Module;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Module\Fixtures\RootModule;
use Dat0r\Tests\Runtime\Module\Fixtures\AggregateModule;
use Dat0r\Tests\Runtime\Module\Fixtures\InvalidRootModule;
use Dat0r\Runtime\Module\IModule;

class ModuleTest extends TestCase
{
    public function testCreateRootModule()
    {
        $module = RootModule::getInstance();

        $this->assertEquals('Article', $module->getName());
        $this->assertEquals(11, $module->getFields()->getSize());
    }

    public function testCreateAggegateModule()
    {
        $module = AggregateModule::getInstance();

        $this->assertEquals(2, $module->getFields()->getSize());
        $this->assertEquals('Paragraph', $module->getName());
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldMethod(IModule $module)
    {
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextField', $module->getField('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\IntegerField', $module->getField('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldsMethodPlain(IModule $module)
    {
        $fields = $module->getFields();

        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\FieldMap', $fields);
        $this->assertEquals(11, $fields->getSize());

        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextField', $fields->getItem('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextField', $fields->getItem('content'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\IntegerField', $fields->getItem('clickCount'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextField', $fields->getItem('author'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextField', $fields->getItem('email'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextCollectionField', $fields->getItem('keywords'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\BooleanField', $fields->getItem('enabled'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\IntegerCollectionField', $fields->getItem('images'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\KeyValueField', $fields->getItem('meta'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\AggregateField', $fields->getItem('paragraph'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\ReferenceField', $fields->getItem('references'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldsMethodFiltered(IModule $module)
    {
        $fields = $module->getFields(array('headline', 'clickCount'));

        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\FieldMap', $fields);
        $this->assertEquals(2, $fields->getSize());

        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\TextField', $fields->getItem('headline'));
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\Type\\IntegerField', $fields->getItem('clickCount'));
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
    public function testInvalidFieldException(IModule $module)
    {
        var_dump(var_export($module->getField('foobar-field-does-not-exist'), true)); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidTypeException
     */
    public function testInvalidDocumentImplementorException()
    {
        $module = InvalidRootModule::getInstance();
        $module->createDocument(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @codeCoverageIgnore
     */
    public static function provideModuleInstances()
    {
        return array(array(RootModule::getInstance()));
    }
}
