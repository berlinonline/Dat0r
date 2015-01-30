<?php

namespace Dat0r\Tests\Runtime\Attribute\KeyValueList;

use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListAttribute;
use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class KeyValueListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new KeyValueListAttribute('keyvalue');
        $this->assertEquals($attribute->getName(), 'keyvalue');
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ 'foo' => 'bar' ];

        $attribute = new KeyValueListAttribute('keyvalue', [ KeyValueListAttribute::OPTION_DEFAULT_VALUE => $data ]);

        $valueholder = $attribute->createValueHolder();
        $this->assertInstanceOf(KeyValueListValueHolder::CLASS, $valueholder);
        $this->assertEquals($data, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $data = [ 'foo' => 'bar' ];
        $foo = $data;
        $bar = $data;
        $bar['asdf'] = 'asdf';

        $attribute = new KeyValueListAttribute('keyvalue', [ KeyValueListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testValueTypeIntegerConstraint()
    {
        $data = [
            'foo' => '1',
            'bar' => '2'
        ];
        $comp = [
            'foo' => 1,
            'bar' => 2
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_CAST_VALUES_TO => KeyValueListAttribute::CAST_TO_INTEGER
        ]);

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($data);
        $this->assertEquals($comp, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($comp));
    }

    public function testValueTypeStringConstraint()
    {
        $data = [
            'foo' => 1,
            'bar' => 2
        ];
        $comp = [
            'foo' => '1',
            'bar' => '2'
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_CAST_VALUES_TO => KeyValueListAttribute::CAST_TO_STRING
        ]);

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($data);
        $this->assertEquals($comp, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($comp));
    }

    public function testValueTypeBooleanConstraint()
    {
        $data = [
            '1' => 0,
            '2' => 1,
            '3' => 2,
            '4' => 'off', // this will be boolean FALSE due to literalizing
            '5' => 'false', // this will be boolean FALSE!
            '6' => ' ',
            '7' => 'meh',
            '8' => true,
            '9' => false
        ];
        $comp = [
            '1' => false,
            '2' => true,
            '3' => true,
            '4' => false,
            '5' => false,
            '6' => true,
            '7' => true,
            '8' => true,
            '9' => false
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_CAST_VALUES_TO => KeyValueListAttribute::CAST_TO_BOOLEAN
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($comp, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($comp));
    }

    public function testValueTypeBooleanWithoutLiteralizingConstraint()
    {
        $data = [
            'foo' => 'off'
        ];
        $comp = [
            'foo' => true
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_CAST_VALUES_TO => KeyValueListAttribute::CAST_TO_BOOLEAN,
            KeyValueListAttribute::OPTION_LITERALIZE => false
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($comp, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($comp));
    }

    public function testMinMaxIntegerConstraint()
    {
        $data = [
            'foo' => '23',
            'bar' => 15
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_CAST_VALUES_TO => KeyValueListAttribute::CAST_TO_INTEGER,
            KeyValueListAttribute::OPTION_MIN => 17,
            KeyValueListAttribute::OPTION_MAX => 20
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
//        var_dump($validation_result->getSeverity());
//        var_dump($validation_result->getViolatedRules()->getSize());
//        var_dump($validation_result->getViolatedRules()->getFirst()->getIncidents());
    }
    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new KeyValueListAttribute('keyvalue');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::CRITICAL, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(new stdClass()),
            array(1)
        );
    }
}
