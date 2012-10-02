<?php

namespace CMF\Tests\Core\Runtime\Module;
use CMF\Tests\Core\Runtime;

use CMF\Core\Runtime\Module;

class ModuleTest extends Runtime\BaseTest
{
    public function testCreate()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \CMF\Core\Runtime\Field\TextField::create('headline')
        ));
        $this->assertEquals('Article', $module->getName());
        $this->assertInstanceOf('CMF\Core\Runtime\Field\TextField', $module->getField('id'));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\TextField', $module->getField('revision'));
    }

    public function testGetField()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \CMF\Core\Runtime\Field\TextField::create('headline'), 
            \CMF\Core\Runtime\Field\IntegerField::create('clickCount')
        ));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\TextField', $module->getField('headline'));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\IntegerField', $module->getField('clickCount'));
    }

    public function testGetFieldsPlain()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \CMF\Core\Runtime\Field\TextField::create('headline'), 
            \CMF\Core\Runtime\Field\TextField::create('content'), 
            \CMF\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $fields = $module->getFields();
        $this->assertInstanceOf('CMF\Core\Runtime\Field\FieldCollection', $fields);
        $this->assertEquals(5, $fields->getSize()); // RootModule's ship with an id and a revision field.
        $this->assertInstanceOf('CMF\Core\Runtime\Field\TextField', $fields->get('headline'));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\TextField', $fields->get('content'));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\IntegerField', $fields->get('clickCount'));
    }

    public function testGetFieldsFiltered()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \CMF\Core\Runtime\Field\TextField::create('headline'), 
            \CMF\Core\Runtime\Field\TextField::create('content'), 
            \CMF\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $fields = $module->getFields(array('headline', 'clickCount'));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\FieldCollection', $fields);
        $this->assertEquals(2, $fields->getSize());
        $this->assertInstanceOf('CMF\Core\Runtime\Field\TextField', $fields->get('headline'));
        $this->assertInstanceOf('CMF\Core\Runtime\Field\IntegerField', $fields->get('clickCount'));
    }

    public function createDocument()
    {
        $module = RootModuleTestProxy::create('Article', array( 
            \CMF\Core\Runtime\Field\TextField::create('headline'), 
            \CMF\Core\Runtime\Field\TextField::create('content'), 
            \CMF\Core\Runtime\Field\IntegerField::create('clickCount')
        ));

        $this->assertInstanceOf('CMF\Core\Runtime\Document', $module->createDocument());
    }
}
