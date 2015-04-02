<?php

namespace Dat0r\Tests\Runtime\Attribute\IntegerList;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IntegerList\IntegerListAttribute;
use Dat0r\Runtime\Attribute\IntegerList\IntegerListValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class IntegerListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new IntegerListAttribute('IntegerList');
        $this->assertEquals($attribute->getName(), 'IntegerList');
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ 1, 2 ];
        $attribute = new IntegerListAttribute('IntegerList', [ IntegerListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(IntegerListValueHolder::CLASS, $valueholder);
        $this->assertEquals([ 1, 2 ], $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $data = [ 1, 2 ];
        $foo = $data;
        $bar = $data;
        $bar[] = 3;

        $attribute = new IntegerListAttribute('IntegerList', [ IntegerListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testSettingBooleanTrueAsValueFails()
    {
        $attribute = new IntegerListAttribute('IntegerListbooltrue');
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(true);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testOctalValues()
    {
        $attribute = new IntegerListAttribute('IntegerListoctalsucceeds', [
            IntegerListAttribute::OPTION_ALLOW_OCTAL => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue([ '010' ]);
        $this->assertEquals([ 8 ], $valueholder->getValue());
    }

    public function testOctalValuesFails()
    {
        $attribute = new IntegerListAttribute('IntegerListoctalfails', [
            IntegerListAttribute::OPTION_ALLOW_OCTAL => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue([ '010' ]);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testHexValues()
    {
        $attribute = new IntegerListAttribute('IntegerListhexsucceeeds', [
            IntegerListAttribute::OPTION_ALLOW_HEX => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue([ '0x10' ]);
        $this->assertEquals([ 16 ], $valueholder->getValue());
    }

    public function testHexValuesFails()
    {
        $attribute = new IntegerListAttribute('IntegerListhexfails', [
            IntegerListAttribute::OPTION_ALLOW_HEX => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue([ '0x10' ]);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testMinMaxConstraint()
    {
        $data = [
            1,
            12,
        ];

        $attribute = new IntegerListAttribute('IntegerListminmax', [
            IntegerListAttribute::OPTION_MIN_VALUE => 3,
            IntegerListAttribute::OPTION_MAX_VALUE => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new IntegerListAttribute('IntegerListminmaxintegerdefaultvalue', [
            IntegerListAttribute::OPTION_MIN_VALUE => 1,
            IntegerListAttribute::OPTION_MAX_VALUE => 5,
            IntegerListAttribute::OPTION_DEFAULT_VALUE => 666
        ]);
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new IntegerListAttribute('IntegerListInvalidValue');
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
