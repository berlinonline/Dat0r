<?php

namespace Dat0r\Tests\Core\Runtime\Module;
use Dat0r\Tests\Core;

use Dat0r\Core\Runtime\Module;

class ModuleTest extends Core\BaseTest
{
    public function testCreateRootModule()
    {
        $module = RootModule::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));
        $module->freeze();

        $this->assertEquals('Article', $module->getName());
        // All RootModules should own fields for id and revision.
        $this->assertEquals(5, $module->getFields()->getSize());
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $module->getField('id'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $module->getField('revision'));
    }

    public function testCreateAggegateModule()
    {
        $module = AggregateModule::create('ArticleAggregate', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));
        $module->freeze();

        $this->assertEquals(3, $module->getFields()->getSize());
        $this->assertEquals('ArticleAggregate', $module->getName());
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testForConsistentFrozenState(Module\IModule $module)
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
    public function testGetFieldMethod(Module\IModule $module)
    {
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $module->getField('headline'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\IntegerField', $module->getField('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldsMethodPlain(Module\IModule $module)
    {
        $fields = $module->getFields();

        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\FieldCollection', $fields);
        $this->assertEquals(5, $fields->getSize()); // RootModule's ship with an id and a revision field.

        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $fields->get('headline'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $fields->get('content'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\IntegerField', $fields->get('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testGetFieldsMethodFiltered(Module\IModule $module)
    {
        $fields = $module->getFields(array('headline', 'clickCount'));

        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\FieldCollection', $fields);
        $this->assertEquals(2, $fields->getSize());

        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $fields->get('headline'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\IntegerField', $fields->get('clickCount'));
    }

    /**
     * @dataProvider provideModuleInstances
     */
    public function testCreateDocumentMethod(Module\IModule $module)
    {
        $document = $module->createDocument();
        $this->assertInstanceOf('Dat0r\Core\Runtime\Document\Document', $document);
    }

    /**
     * @dataProvider provideModuleInstances
     * @expectedException Dat0r\Core\Runtime\Module\InvalidFieldException
     */
    public function testInvalidFieldException(Module\IModule $module)
    {
        $module->getField('foobar-field-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Core\Runtime\Error\InvalidImplementorException
     */
    public function testInvalidDocumentImplementorException()
    {
        $module = InvalidRootModule::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));
        $module->freeze();

        $module->createDocument(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @codeCoverageIgnore
     */
    public static function provideModuleInstances()
    {
        $module = RootModule::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $module->freeze();

        return array(array($module));
    }
}
