<?php

namespace Dat0r\Tests\Runtime\Attribute\KeyValueList;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\Integer\IntegerAttribute;
use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListAttribute;
use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;
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
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_INTEGER
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
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_TEXT
        ]);

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($data);
        $this->assertEquals($comp, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($comp));
    }

    public function testTextRuleOptionsForValueTypeStringConstraints()
    {
        $data = [ 'foo' => "bar\t\r\nbaz " ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_TEXT,
            TextRule::OPTION_REJECT_INVALID_UTF8 => false,
            TextRule::OPTION_STRIP_INVALID_UTF8 => false,
            TextRule::OPTION_STRIP_NULL_BYTES => false,
            TextRule::OPTION_TRIM => false,
            TextRule::OPTION_STRIP_CONTROL_CHARACTERS => false,
            TextRule::OPTION_ALLOW_CRLF => true,
            TextRule::OPTION_ALLOW_TAB => true,
            TextRule::OPTION_NORMALIZE_NEWLINES => true
        ]);

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($data);
        $val = $valueholder->getValue();
        $this->assertEquals("bar\t\nbaz ", $val['foo']);
        $this->assertTrue($valueholder->sameValueAs($data));
    }

    public function testValueTypeFloatConstraint()
    {
        $data = [
            'foo' => '1',
            'bar' => '2'
        ];
        $comp = [
            'foo' => 1.0,
            'bar' => 2.0
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_FLOAT
        ]);

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($data);
        $this->assertEquals($comp, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($comp));
        $asdf = $valueholder->getValue();
        $this->assertTrue(is_float($asdf['bar']));
    }

    public function testValueTypeBooleanConstraint()
    {
        $data = [
            'a' => 0, // false
            'b' => 1, // true
            'c' => "1", // true
            'd' => 'off', // false
            'e' => 'false', // false
            'f' => '', // false
            'g' => 'no', // false
            'h' => true,
            'i' => false,
            'j' => 'on', // true
            'k' => 'true', // true
            'l' => 'yes' // true
        ];
        $comp = [
            'a' => false,
            'b' => true,
            'c' => true,
            'd' => false,
            'e' => false,
            'f' => false,
            'g' => false,
            'h' => true,
            'i' => false,
            'j' => true,
            'k' => true,
            'l' => true
        ];

        $attribute = new KeyValueListAttribute('keyvalue', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_BOOLEAN
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
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_INTEGER,
            IntegerAttribute::OPTION_MIN => 17,
            IntegerAttribute::OPTION_MAX => 20
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            'bar' => '15',
            'foo' => '1234567890',
        ];

        $attribute = new KeyValueListAttribute('keyvalueminmaxstringlength', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_TEXT,
            TextRule::OPTION_MIN => 3,
            TextRule::OPTION_MAX => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        //$vr = $validation_result->getViolatedRules()->getFirst();
        // var_dump($vr);
        // var_dump(get_class_methods($vr));
        // var_dump($vr->getName());
        // var_dump($vr->getIncidents()->getSize());
        // var_dump($vr->getIncidents());
    }

    public function testMaxCountConstraint()
    {
        $data = [ 'foo' => 'bar', 'blah' => 'blub' ];

        $attribute = new KeyValueListAttribute('keyvaluemaxcount', [
            KeyValueListAttribute::OPTION_MAX_COUNT => 1
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $attribute->getNullValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ 'foo' => 'bar' ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }

    public function testToNativeRoundtripWithBooleanFlags()
    {
        $attribute = new KeyValueListAttribute('flags', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_BOOLEAN
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(
            [ 'a' => 'on', 'b' => true, 'c' => 'yes', 'd' => 'no', 'e' => 'false', 'f' => false ]
        );
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals(
            [ 'a' => true, 'b' => true, 'c' => true, 'd' => false, 'e' => false, 'f' => false ],
            $valueholder->getValue()
        );
        $this->assertEquals(
            [ 'a' => true, 'b' => true, 'c' => true, 'd' => false, 'e' => false, 'f' => false ],
            $valueholder->toNative()
        );

        $valueholder->setValue($valueholder->toNative());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals(
            [ 'a' => true, 'b' => true, 'c' => true, 'd' => false, 'e' => false, 'f' => false ],
            $valueholder->toNative()
        );
        $this->assertEquals(
            [ 'a' => true, 'b' => true, 'c' => true, 'd' => false, 'e' => false, 'f' => false ],
            $valueholder->getValue()
        );
    }

    public function testAllowedValuesConstraintFails()
    {
        $attribute = new KeyValueListAttribute('roles', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_TEXT,
            KeyValueListAttribute::OPTION_ALLOWED_VALUES => [ 'bar' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo' => 'blah']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedKeysConstraintFails()
    {
        $attribute = new KeyValueListAttribute('roles', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_TEXT,
            KeyValueListAttribute::OPTION_ALLOWED_KEYS => [ 'bar' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo' => 'bar']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedPairsConstraintFails()
    {
        $attribute = new KeyValueListAttribute('roles', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_TEXT,
            KeyValueListAttribute::OPTION_ALLOWED_VALUES => [ 'bar' => 'foo' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo' => 'bar']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);

        $attribute = new KeyValueListAttribute('keyvalueinvalidintegerdefaultvalue', [
            KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_INTEGER,
            IntegerAttribute::OPTION_MIN => 1,
            IntegerAttribute::OPTION_MAX => 5,
            KeyValueListAttribute::OPTION_DEFAULT_VALUE => 666
        ]);

        $attribute->getDefaultValue();
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
        return [
            [null],
            [false],
            [true],
            [new stdClass()],
            [1],
            ['' => 'asdf']
        ];
    }
}
