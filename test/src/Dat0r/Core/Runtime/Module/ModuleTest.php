<?php

namespace Dat0r\Tests\Core\Runtime\Module;
use Dat0r\Tests\Core\BaseTest;

use Dat0r\Core\Runtime\Module\IModule;
use Dat0r\Core\Runtime\Field;

class ModuleTest extends BaseTest
{
    public function testCreateRootModule()
    {
        $module = RootModule::getInstance();

        $this->assertEquals('Article', $module->getName());
        // All RootModules should own fields for id and revision.
        $this->assertEquals(5, $module->getFields()->getSize());
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\TextField', $module->getField('id'));
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\TextField', $module->getField('revision'));
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
    public function testForConsistentFrozenState(IModule $module)
    {
        $fields = $module->getFields();

        $this->assertTrue($module->isFrozen());
        $this->assertTrue($fields->isFrozen());

        foreach ($fields as $field)
        {
            $this->assertTrue($field->isFrozen());
        }
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldMethod(IModule $module)
    {
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\TextField', $module->getField('headline'));
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\IntegerField', $module->getField('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldsMethodPlain(IModule $module)
    {
        $fields = $module->getFields();

        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\FieldCollection', $fields);
        $this->assertEquals(5, $fields->getSize()); // RootModule's ship with an id and a revision field.

        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\TextField', $fields->get('headline'));
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\TextField', $fields->get('content'));
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\IntegerField', $fields->get('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldsMethodFiltered(IModule $module)
    {
        $fields = $module->getFields(array('headline', 'clickCount'));

        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\FieldCollection', $fields);
        $this->assertEquals(2, $fields->getSize());

        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\TextField', $fields->get('headline'));
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\Field\\IntegerField', $fields->get('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testCreateDocumentMethod(IModule $module)
    {
        $document = $module->createDocument();
        $this->assertInstanceOf('Dat0r\\Core\Runtime\\Document\\Document', $document);
    }

    /**
     * @dataProvider provideModuleInstances
     * @expectedException Dat0r\Core\Runtime\Module\InvalidFieldException
     */
    public function testInvalidFieldException(IModule $module)
    {
        $module->getField('foobar-field-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Core\Runtime\Error\InvalidImplementorException
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
