<?php

namespace Dat0r\Tests\Runtime\Attribute\Choice;

use Dat0r\Runtime\Attribute\Choice\ChoiceAttribute;
use Dat0r\Runtime\Attribute\Choice\ChoiceValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;

class ChoiceAttributeTest extends TestCase
{
    const FIELDNAME = 'role';

    public function testCreate()
    {
        $attribute = new ChoiceAttribute(self::FIELDNAME);
        $this->assertEquals($attribute->getName(), self::FIELDNAME);
    }

    public function testCreateValue()
    {
        $text_attribute = new ChoiceAttribute(self::FIELDNAME);
        $valueholder = $text_attribute->createValueHolder();
        $this->assertInstanceOf(ChoiceValueHolder::CLASS, $valueholder);
        $valueholder->setValue('omgomgomg');
        $this->assertEquals('omgomgomg', $valueholder->getValue());
    }

    public function testValidationSuccess()
    {
        $text_attribute = new ChoiceAttribute(
            self::FIELDNAME,
            array(ChoiceAttribute::OPTION_MIN_LENGTH => 3, ChoiceAttribute::OPTION_MAX_LENGTH => 10)
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::SUCCESS);
    }

    public function testValidationError()
    {
        $text_attribute = new ChoiceAttribute(
            self::FIELDNAME,
            array(ChoiceAttribute::OPTION_MIN_LENGTH => 3, ChoiceAttribute::OPTION_MAX_LENGTH => 5)
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::ERROR);
    }

    public function testAllowedValuesConstraintFails()
    {
        $attribute = new ChoiceAttribute('role', [
            ChoiceAttribute::OPTION_ALLOWED_VALUES => [ 'administrator', 'editor' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue('foo');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedValuesConstraintSucceeds()
    {
        $attribute = new ChoiceAttribute('role', [
            ChoiceAttribute::OPTION_ALLOWED_VALUES => [ 'administrator', 'editor' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(' editor ');
        $this->assertEquals('editor', $valueholder->getValue());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() === IncidentInterface::SUCCESS);
    }
}
