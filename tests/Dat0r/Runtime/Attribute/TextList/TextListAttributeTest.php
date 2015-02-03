<?php

namespace Dat0r\Tests\Runtime\Attribute\TextList;

use Dat0r\Common\Error\InvalidConfigException;
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
        $data = [ 'foo' => 'bar' ]; // key will be ignored

        $attribute = new TextListAttribute('TextList', [ TextListAttribute::OPTION_DEFAULT_VALUE => $data ]);

        $valueholder = $attribute->createValueHolder();
        $this->assertInstanceOf(TextListValueHolder::CLASS, $valueholder);
        $this->assertEquals([ 'bar' ], $valueholder->getValue());
    }

    public function testSetValue()
    {
        $data = [ 'foo', 'bar' ];

        $attribute = new TextListAttribute('TextList', [ TextListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder();

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

        $attribute = new TextListAttribute('TextList', [ TextListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            '15',
            '1234567890',
        ];

        $attribute = new TextListAttribute('TextListminmaxstringlength', [
            TextListAttribute::OPTION_MIN => 3,
            TextListAttribute::OPTION_MAX => 5
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

        $attribute = new TextListAttribute('TextListminmaxintegerdefaultvalue', [
            TextListAttribute::OPTION_MIN => 1,
            TextListAttribute::OPTION_MAX => 5,
            TextListAttribute::OPTION_DEFAULT_VALUE => 666
        ]);

        $attribute->getDefaultValue();
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
        return array(
            array(null),
            array(false),
            array(true),
            array(new stdClass()),
            array(1)
        );
    }
}
