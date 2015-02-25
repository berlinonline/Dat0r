<?php

namespace Dat0r\Tests\Runtime\Attribute\Integer;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\Integer\IntegerAttribute;
use Dat0r\Runtime\Attribute\Integer\IntegerValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class IntegerAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new IntegerAttribute('Integer');
        $this->assertEquals($attribute->getName(), 'Integer');
        $this->assertEquals(0, $attribute->getNullValue());
    }

    public function testCreateValueWithDefaultValues()
    {
        $attribute = new IntegerAttribute('Integer', [ IntegerAttribute::OPTION_DEFAULT_VALUE => 123 ]);
        $valueholder = $attribute->createValueHolder();
        $this->assertInstanceOf(IntegerValueHolder::CLASS, $valueholder);
        $this->assertEquals(123, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $attribute = new IntegerAttribute('Integer', [ IntegerAttribute::OPTION_DEFAULT_VALUE => 1337 ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals(1337, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs('1337'));
        $this->assertFalse($valueholder->sameValueAs(1338));
    }

    public function testSettingBooleanTrueAsValueFails()
    {
        $attribute = new IntegerAttribute('Integerbooltrue');
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(true);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testOctalValues()
    {
        $attribute = new IntegerAttribute('octal', [
            IntegerAttribute::OPTION_ALLOW_OCTAL => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('010');
        $this->assertEquals(8, $valueholder->getValue());
    }

    public function testOctalValuesFails()
    {
        $attribute = new IntegerAttribute('octalfails', [
            IntegerAttribute::OPTION_ALLOW_OCTAL => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('010');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testHexValues()
    {
        $attribute = new IntegerAttribute('hex', [
            IntegerAttribute::OPTION_ALLOW_HEX => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('0x10');
        $this->assertEquals(16, $valueholder->getValue());
    }

    public function testHexValuesFails()
    {
        $attribute = new IntegerAttribute('hexfails', [
            IntegerAttribute::OPTION_ALLOW_HEX => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('0x10');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testMinMaxConstraint()
    {
        $attribute = new IntegerAttribute('Integerminmax', [
            IntegerAttribute::OPTION_MIN => 3,
            IntegerAttribute::OPTION_MAX => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue(1337);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new IntegerAttribute('integerinvaliddefaultvalue', [
            IntegerAttribute::OPTION_MIN => 1,
            IntegerAttribute::OPTION_MAX => 5,
            IntegerAttribute::OPTION_DEFAULT_VALUE => 666
        ]);
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new IntegerAttribute('IntegerInvalidValue');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(new stdClass())
        );
    }
}