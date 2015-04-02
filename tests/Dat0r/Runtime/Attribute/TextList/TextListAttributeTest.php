<?php

namespace Dat0r\Tests\Runtime\Attribute\TextList;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Runtime\Attribute\TextList\TextListAttribute;
use Dat0r\Runtime\Attribute\TextList\TextListValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class TextListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new TextListAttribute('TextList');
        $this->assertEquals($attribute->getName(), 'TextList');
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ 'foo' => "foo\x00bar" ]; // key will be ignored

        $attribute = new TextListAttribute('TextList', [ TextListAttribute::OPTION_DEFAULT_VALUE => $data ]);

        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TextListValueHolder::CLASS, $valueholder);
        $this->assertEquals([ 'foobar' ], $valueholder->getValue());
    }

    public function testTextRuleOptionsForTextListAttribute()
    {
        $data = [ "\x00bar\nfoo" ];

        $attribute = new TextListAttribute('TextList', [
            TextListAttribute::OPTION_DEFAULT_VALUE => $data,
            TextListAttribute::OPTION_STRIP_NULL_BYTES => false,
            TextListAttribute::OPTION_TRIM => false,
            TextListAttribute::OPTION_ALLOW_CRLF => true
        ]);

        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TextListValueHolder::CLASS, $valueholder);
        $this->assertEquals([ "\x00bar\nfoo" ], $valueholder->getValue());
    }

    public function testSetValue()
    {
        $data = [ 'foo', 'bar' ];

        $attribute = new TextListAttribute('TextList', [ TextListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder(true);
        $this->assertEquals($data, $valueholder->getValue());

        $new = [ 'foo', 'bar', '' ];

        $valueholder->setValue($new);

        $this->assertEquals($new, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($new));
        $this->assertFalse($valueholder->sameValueAs($data));
    }

    public function testValueComparison()
    {
        $data = [ 'bar' ];
        $foo = $data;
        $bar = $data;
        $bar[] = 'asdf';

        $attribute = new TextListAttribute('valuecomparison', [ TextListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testMinCountConstraint()
    {
        $data = [ ];

        $attribute = new TextListAttribute('TextListmincount', [
            TextListAttribute::OPTION_MIN_COUNT => 1
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ 'asdf' ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }

    public function testMaxCountConstraint()
    {
        $data = [ 'foo', 'bar' ];

        $attribute = new TextListAttribute('TextListmaxcount', [
            TextListAttribute::OPTION_MAX_COUNT => 1
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $attribute->getNullValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ 'foo' ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            '15',
            '1234567890',
        ];

        $attribute = new TextListAttribute('TextListminmaxstringlength', [
            TextListAttribute::OPTION_MIN_LENGTH => 3,
            TextListAttribute::OPTION_MAX_LENGTH => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedValuesConstraintFails()
    {
        $attribute = new TextListAttribute('roles', [
            TextListAttribute::OPTION_ALLOWED_VALUES => [ 'bar' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo']);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    /**
     * @dataProvider provideInvalidConfigDefaultValues
     * @expectedExceptionMessage Given value for attribute 'TextListminmaxdefaultvalue' on entity type 'undefined' is not valid.
     */
    public function testThrowsOnInvalidDefaultValueInConfig($expected_exception, $invalid_default_value)
    {
        $this->setExpectedException($expected_exception);

        $attribute = new TextListAttribute('TextListminmaxdefaultvalue', [
            TextListAttribute::OPTION_MIN_COUNT => 1,
            TextListAttribute::OPTION_MAX_COUNT => 5,
            TextListAttribute::OPTION_DEFAULT_VALUE => $invalid_default_value
        ]);

        $attribute->getDefaultValue();
    }

    public function testGetNullValueOnMissingDefaultValueInConfig()
    {
        $attribute = new TextListAttribute('TextListmissingdefaultvalue');

        $this->assertInternalType('array', $attribute->getDefaultValue());
        $this->assertCount(0, $attribute->getDefaultValue());
    }

    public function testThrowsOnMissingDefaultValueInConfig()
    {
        $attribute = new TextListAttribute('TextListwrongtypeargument');

        $this->setExpectedException(InvalidTypeException::CLASS);

        $valueholder = $attribute->createValueHolder('false');
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new TextListAttribute('TextList');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::CRITICAL, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ null ],
            [ false ],
            [ true ],
            [ new stdClass() ],
            [ 1 ]
        ];
    }

    public function provideInvalidConfigDefaultValues()
    {
        return [
            [ BadValueException::CLASS, [] ],
            [ BadValueException::CLASS, [ 'i', 'n', 'v', 'a', 'l', 'i', 'd'] ]
        ];
    }
}
