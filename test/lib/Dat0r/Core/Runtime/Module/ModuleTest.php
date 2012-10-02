<?php

namespace Dat0r\Tests\Core\Runtime\Module;
use Dat0r\Tests\Core\Runtime;

use Dat0r\Core\Runtime\Module;

class ModuleTest extends Runtime\BaseTest
{
    public function testCreate()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline')
        ));
        $this->assertEquals('Article', $module->getName());
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $module->getField('id'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $module->getField('revision'));
    }

    public function testGetField()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $module->getField('headline'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\IntegerField', $module->getField('clickCount'));
    }

    public function testGetFieldsPlain()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $fields = $module->getFields();
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\FieldCollection', $fields);
        $this->assertEquals(5, $fields->getSize()); // RootModule's ship with an id and a revision field.
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $fields->get('headline'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $fields->get('content'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\IntegerField', $fields->get('clickCount'));
    }

    public function testGetFieldsFiltered()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $fields = $module->getFields(array('headline', 'clickCount'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\FieldCollection', $fields);
        $this->assertEquals(2, $fields->getSize());
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\TextField', $fields->get('headline'));
        $this->assertInstanceOf('Dat0r\Core\Runtime\Field\IntegerField', $fields->get('clickCount'));
    }

    public function createDocument()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('headline'), 
            \Dat0r\Core\Runtime\Field\TextField::create('content'), 
            \Dat0r\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $this->assertInstanceOf('Dat0r\Core\Runtime\Document', $module->createDocument());
    }
}
