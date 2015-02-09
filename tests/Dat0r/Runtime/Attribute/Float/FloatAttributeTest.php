<?php

namespace Dat0r\Tests\Runtime\Attribute\Float;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Attribute\Float\FloatAttribute;
use Dat0r\Runtime\Attribute\Float\FloatValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class FloatAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new FloatAttribute('Float');
        $this->assertEquals($attribute->getName(), 'Float');
        $this->assertEquals(0, $attribute->getNullValue());
    }

    public function testCreateValueWithDefaultValues()
    {
        $attribute = new FloatAttribute('Float', [ FloatAttribute::OPTION_DEFAULT_VALUE => 123.456 ]);
        $valueholder = $attribute->createValueHolder();
        $this->assertInstanceOf(FloatValueHolder::CLASS, $valueholder);
        $this->assertEquals(123.456, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $attribute = new FloatAttribute('Float', [ FloatAttribute::OPTION_DEFAULT_VALUE => 1337.456 ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertTrue(abs(1337.456-$valueholder->getValue()) < 0.00000000000001);
        $this->assertTrue($valueholder->sameValueAs('1337.456'));
        $this->assertFalse($valueholder->sameValueAs(1337.455999));
        $this->assertFalse($valueholder->sameValueAs(1337.456001));
    }

    public function testSettingBooleanTrueAsValueFails()
    {
        $attribute = new FloatAttribute('Floatbooltrue');
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(true);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testNanValue()
    {
        $attribute = new FloatAttribute('nan', [
            FloatAttribute::OPTION_ALLOW_NAN => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(acos(1.01));
        $this->assertEquals('NAN', (string)$valueholder->getValue());
    }

    public function testNanValuesFails()
    {
        $attribute = new FloatAttribute('nanfails', [
            FloatAttribute::OPTION_ALLOW_NAN => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(acos(1.01));
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testInfinityValues()
    {
        $attribute = new FloatAttribute('inf', [
            FloatAttribute::OPTION_ALLOW_INFINITY => true
        ]);

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(log(0));
        $this->assertEquals('-INF', (string)$valueholder->getValue());

        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(-log(0));
        $this->assertEquals('INF', (string)$valueholder->getValue());
    }

    public function testInfinityValueFails()
    {
        $attribute = new FloatAttribute('inffails', [
            FloatAttribute::OPTION_ALLOW_INFINITY => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(log(0));
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testMinMaxConstraint()
    {
        $attribute = new FloatAttribute('Floatminmax', [
            FloatAttribute::OPTION_MIN => 3,
            FloatAttribute::OPTION_MAX => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue(2.9999999997);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testLowPrecisionComparison()
    {
        $attribute = new FloatAttribute('lowprecisionfloat', [ FloatAttribute::OPTION_PRECISION_DIGITS => "3" ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(3.14159);
        $this->assertTrue($valueholder->sameValueAs(3.1419));
        $this->assertTrue($valueholder->sameValueAs(3.1410));
        $this->assertTrue($valueholder->sameValueAs(3.142));
        $this->assertFalse($valueholder->sameValueAs(3.140));
        $this->assertFalse($valueholder->sameValueAs(3.143));
    }

    public function testTooHighPrecisionThrows()
    {
        $this->setExpectedException(RuntimeException::CLASS);
        $attribute = new FloatAttribute('tonative', [
            FloatAttribute::OPTION_PRECISION_DIGITS => ini_get('precision') + 5
        ]);
        $valueholder = $attribute->createValueHolder();
    }

    public function testToNativeNan()
    {
        $attribute = new FloatAttribute('nan', [
            FloatAttribute::OPTION_ALLOW_NAN => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(acos(1.01));
        $this->assertEquals('NAN', $valueholder->toNative());
    }

    public function testToNativeInf()
    {
        $attribute = new FloatAttribute('inf', [
            FloatAttribute::OPTION_ALLOW_INFINITY => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(log(0));
        $this->assertEquals('-INF', $valueholder->toNative());
    }

    public function testToNativeRoundtrip()
    {
        $attribute = new FloatAttribute('tonative', [
            FloatAttribute::OPTION_PRECISION_DIGITS => 8
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(1337.1234567890123);
        $this->assertTrue(is_float($valueholder->getValue()));
        $this->assertEquals('1337.12345679', sprintf('%0.8f', $valueholder->getValue()));
        $result = $valueholder->setValue($valueholder->toNative());
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertTrue(is_float($valueholder->getValue()));
        $this->assertEquals('1337.12345679', sprintf('%0.8f', $valueholder->getValue()));
    }

    public function testToNativeInfRoundtrip()
    {
        $attribute = new FloatAttribute('inf', [
            FloatAttribute::OPTION_ALLOW_INFINITY => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(log(0));
        $this->assertEquals('-INF', $valueholder->toNative());
        $result = $valueholder->setValue($valueholder->toNative());
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertTrue(is_float($valueholder->getValue()));
        $this->assertEquals('-INF', $valueholder->toNative());
    }

    public function testToNativeNanRoundtrip()
    {
        $attribute = new FloatAttribute('nan', [
            FloatAttribute::OPTION_ALLOW_NAN => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue("NAN");
        $this->assertTrue(is_float($valueholder->getValue()));
        $this->assertEquals('NAN', $valueholder->toNative());
        $result = $valueholder->setValue($valueholder->toNative());
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertTrue(is_float($valueholder->getValue()));
        $this->assertEquals('NAN', $valueholder->toNative());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new FloatAttribute('Floatinvaliddefaultvalue', [
            FloatAttribute::OPTION_MIN => 1,
            FloatAttribute::OPTION_MAX => 5,
            FloatAttribute::OPTION_DEFAULT_VALUE => 5.00000001
        ]);
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new FloatAttribute('FloatInvalidValue');
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
