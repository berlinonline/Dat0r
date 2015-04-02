<?php

namespace Dat0r\Tests\Runtime\Attribute\BooleanList;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\BooleanList\BooleanListAttribute;
use Dat0r\Runtime\Attribute\BooleanList\BooleanListValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class BooleanListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new BooleanListAttribute('BooleanList');
        $this->assertEquals($attribute->getName(), 'BooleanList');
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ true, false ];
        $attribute = new BooleanListAttribute('BooleanList', [ BooleanListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(BooleanListValueHolder::CLASS, $valueholder);
        $this->assertEquals([ true, false ], $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $data = [ 'on', false ];
        $attribute = new BooleanListAttribute('BooleanList', [ BooleanListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals([ true, false ], $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs([ true, false ]));
        $this->assertFalse($valueholder->sameValueAs([ false , true]));
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new BooleanListAttribute('BooleanListInvalidDefault', [
            BooleanListAttribute::OPTION_DEFAULT_VALUE => 666
        ]);
        $attribute->getDefaultValue();
    }

    public function testThrowsOnFunnyString()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new BooleanListAttribute('BooleanListInvalidDefault', [
            BooleanListAttribute::OPTION_DEFAULT_VALUE => new stdClass()
        ]);
        $attribute->getDefaultValue();
    }

    public function testToNativeRoundtrip()
    {
        $attribute = new BooleanListAttribute('flags');
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(['on', true, 'yes', 'no', 'false', false]);
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals([ true, true, true, false, false, false ], $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals([ true, true, true, false, false, false ], $valueholder->toNative());
        $this->assertNotEquals([ true, true, false, true, false, false ], $valueholder->toNative());
    }

    public function testMaxCountConstraint()
    {
        $data = [ true, false ];

        $attribute = new BooleanListAttribute('keyvaluemaxcount', [
            BooleanListAttribute::OPTION_MAX_COUNT => 1
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ true ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }
    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new BooleanListAttribute('BooleanListInvalidValue');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return array(
            array('null'),
            array('2'),
            array('nottrue'),
            array(new stdClass())
        );
    }
}
