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
    const ATTR_NAME = 'Integer';

    public function testCreate()
    {
        $attribute = new IntegerAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($attribute->getName(), self::ATTR_NAME);
        $this->assertEquals(0, $attribute->getNullValue());
    }

    public function testCreateValueWithDefaultValues()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_DEFAULT_VALUE => 123 ]
        );
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(IntegerValueHolder::CLASS, $valueholder);
        $this->assertEquals(123, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_DEFAULT_VALUE => 1337 ]
        );
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals(1337, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs('1337'));
        $this->assertFalse($valueholder->sameValueAs(1338));
    }

    public function testSettingBooleanTrueAsValueFails()
    {
        $attribute = new IntegerAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(true);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testOctalValues()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_OCTAL => true ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('010');
        $this->assertEquals(8, $valueholder->getValue());
    }

    public function testOctalValuesFails()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_OCTAL => false ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('010');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testHexValues()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_HEX => true ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('0x10');
        $this->assertEquals(16, $valueholder->getValue());
    }

    public function testHexValuesFails()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_HEX => false ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('0x10');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testMinMaxConstraint()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [
                IntegerAttribute::OPTION_MIN_VALUE => 3,
                IntegerAttribute::OPTION_MAX_VALUE => 5
            ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue(1337);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [
                IntegerAttribute::OPTION_MIN_VALUE => 1,
                IntegerAttribute::OPTION_MAX_VALUE => 5,
                IntegerAttribute::OPTION_DEFAULT_VALUE => 666
            ]
        );
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new IntegerAttribute(self::ATTR_NAME, $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ null ],
            [ false ],
            [ true ],
            [ new stdClass() ]
        ];
    }
}
