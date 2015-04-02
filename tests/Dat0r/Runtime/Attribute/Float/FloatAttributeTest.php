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
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(FloatValueHolder::CLASS, $valueholder);
        $this->assertEquals(123.456, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $attribute = new FloatAttribute('Float', [ FloatAttribute::OPTION_DEFAULT_VALUE => 1337.456 ]);
        $valueholder = $attribute->createValueHolder(true);

        $this->assertTrue(abs(1337.456-$valueholder->getValue()) < 0.0000000001);
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
        $this->assertTrue('0.000' === sprintf('%0.3f', $valueholder->getValue()));
    }

    public function testThousandSeparator()
    {
        $attribute = new FloatAttribute('nan', [
            FloatAttribute::OPTION_ALLOW_THOUSAND_SEPARATOR => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('1,200.123');
        $this->assertEquals('1200.123', sprintf('%0.3f', $valueholder->getValue()));
    }

    public function testThousandSeparatorFails()
    {
        $attribute = new FloatAttribute('nan', [
            FloatAttribute::OPTION_ALLOW_THOUSAND_SEPARATOR => false
        ]);
        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue('1,200.123');
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
        $this->assertTrue('0.000' === sprintf('%0.3f', $valueholder->getValue()));
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
            FloatAttribute::OPTION_MIN_VALUE => 3,
            FloatAttribute::OPTION_MAX_VALUE => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue(2.9999999997);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testLowPrecisionComparison()
    {
        $attribute = new FloatAttribute('lowprecisionfloat', [ FloatAttribute::OPTION_PRECISION_DIGITS => "4" ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(3.14159);
        $this->assertTrue($valueholder->sameValueAs(3.14160));
        $this->assertTrue($valueholder->sameValueAs(3.14158));
        $this->assertFalse($valueholder->sameValueAs(3.140));
        $this->assertTrue($valueholder->sameValueAs(3.141));
        $this->assertTrue($valueholder->sameValueAs(3.142));
        $this->assertFalse($valueholder->sameValueAs(3.143));
    }

    public function testComparisonNearZero()
    {
        $attribute = new FloatAttribute('zerocomp', [
            FloatAttribute::OPTION_ALLOW_INFINITY => true,
            FloatAttribute::OPTION_ALLOW_NAN => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(0.0);
        $this->assertTrue($valueholder->sameValueAs(0.0));
        $this->assertTrue($valueholder->sameValueAs(-0.0));
        $this->assertTrue($valueholder->sameValueAs("1e-58"));
        $this->assertFalse($valueholder->sameValueAs("1e-40"));
        $this->assertFalse($valueholder->sameValueAs(-0.00000001));
        $this->assertFalse($valueholder->sameValueAs(log(0)));
        $this->assertFalse($valueholder->sameValueAs(-log(0)));
        $this->assertFalse($valueholder->sameValueAs(acos(1.01)));
    }

    public function testComparisonNearZero2()
    {
        $attribute = new FloatAttribute('zerocomp', [
            FloatAttribute::OPTION_ALLOW_INFINITY => true,
            FloatAttribute::OPTION_ALLOW_NAN => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(-0.0000000000000000000001000001);
        $this->assertFalse($valueholder->sameValueAs(-0.0000000000000000000001000002));
    }

    public function testComparisonInfinity()
    {
        $attribute = new FloatAttribute('infcomp', [
            FloatAttribute::OPTION_ALLOW_INFINITY => true
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(log(0)); // -INF
        $this->assertTrue($valueholder->sameValueAs(log(0)));
        $this->assertTrue($valueholder->sameValueAs(log(0) + 9999999)); // -INF
        $this->assertTrue($valueholder->sameValueAs(log(0) - 9999999)); // -INF
        $this->assertFalse($valueholder->sameValueAs(-log(0))); // INF
    }

    public function testSomeFloatEqualityComparisons()
    {
        $attribute = new FloatAttribute('somefloatcomp', []);
        $valueholder = $attribute->createValueHolder();

        $valueholder->setValue(0.3);
        $this->assertTrue($valueholder->sameValueAs(0.1 + 0.2));

        $valueholder->setValue(0.1 + 0.2);
        $this->assertTrue($valueholder->sameValueAs(0.3));

        $valueholder->setValue(0.1 + 0.07);
        $this->assertTrue($valueholder->sameValueAs(1 - 0.83));
    }

    public function testFloatMinValuesAroundZero()
    {
        $attribute = new FloatAttribute('somefloatmincomp', []);
        $valueholder = $attribute->createValueHolder();

        $valueholder->setValue(FloatValueHolder::FLOAT_MIN);
        $this->assertFalse($valueholder->sameValueAs(-FloatValueHolder::FLOAT_MIN));

        $valueholder->setValue(0.0);
        $this->assertFalse($valueholder->sameValueAs(FloatValueHolder::FLOAT_MIN));
        $this->assertFalse($valueholder->sameValueAs(-FloatValueHolder::FLOAT_MIN));

        $valueholder->setValue(10 * FloatValueHolder::FLOAT_MIN);
        $this->assertFalse($valueholder->sameValueAs(-10 * FloatValueHolder::FLOAT_MIN));

        $valueholder->setValue(10000 * FloatValueHolder::FLOAT_MIN);
        $this->assertFalse($valueholder->sameValueAs(-10000 * FloatValueHolder::FLOAT_MIN));
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
            FloatAttribute::OPTION_MIN_VALUE => 1,
            FloatAttribute::OPTION_MAX_VALUE => 5,
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
