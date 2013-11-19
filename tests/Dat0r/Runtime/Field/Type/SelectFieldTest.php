<?php

namespace Dat0r\Tests\Runtime\Field;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Field\Type\SelectField;
use Dat0r\Runtime\Validator\Result\IIncident;

class SelectFieldTest extends TestCase
{
    public function testCreate()
    {
        $select_field = $this->createField();
        $this->assertEquals($select_field->getName(), 'city');
    }

    public function testCreateValueHolder()
    {
        $select_field = $this->createField();
        $value_holder = $select_field->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\SelectValueHolder', $value_holder);
    }

    public function testValidationSuccess()
    {
        $select_field = $this->createField();
        $value_holder = $select_field->createValueHolder();
        $result = $value_holder->setValue('berlin');

        $this->assertEquals($result->getSeverity(), IIncident::SUCCESS);
        $this->assertEquals(array('berlin'), $value_holder->getValue());
    }

    public function testValidationError()
    {
        $select_field = $this->createField();
        $value_holder = $select_field->createValueHolder();
        $result = $value_holder->setValue('meh');

        $this->assertEquals($result->getSeverity(), IIncident::ERROR);
        $this->assertEquals(null, $value_holder->getValue());
    }

    protected function createField()
    {
        return SelectField::create(
            'city',
            array(
                'select_options' => array('new-york' => 'New York', 'berlin' => 'Berlin', 'boston' => 'Boston')
            )
        );
    }
}
