<?php

namespace Dat0r\Tests\Runtime\Attribute\IntegerList;

use Dat0r\Common\Error\InvalidConfigException;
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
        $valueholder = $attribute->createValueHolder();
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
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testMinMaxConstraint()
    {
        $data = [
            1,
            12,
        ];

        $attribute = new IntegerListAttribute('IntegerListminmax', [
            IntegerListAttribute::OPTION_MIN => 3,
            IntegerListAttribute::OPTION_MAX => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(InvalidConfigException::CLASS);
        $attribute = new IntegerListAttribute('IntegerListminmaxintegerdefaultvalue', [
            IntegerListAttribute::OPTION_MIN => 1,
            IntegerListAttribute::OPTION_MAX => 5,
            IntegerListAttribute::OPTION_DEFAULT_VALUE => 666
        ]);
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new IntegerListAttribute('IntegerList');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::CRITICAL, $result->getSeverity(), $assert_message);
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
