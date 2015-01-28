<?php

namespace Dat0r\Tests\Runtime\Attribute\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Timestamp\TimestampAttribute;
use Dat0r\Runtime\Attribute\Timestamp\TimestampValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use DateTime;
use DateTimeImmutable;
use stdClass;

class TimestampAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new TimestampAttribute('publishedAt');
        $this->assertEquals($attribute->getName(), 'publishedAt');
    }

    public function testCreateValueAcceptsString()
    {
        $datetime = '2014-12-31T13:45:55.123+01:00';
        $datetime_in_utc = '2014-12-31T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute('publishedAt');
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
        $value->setValue($datetime);
        $this->assertInstanceOf('\\DateTimeImmutable', $value->getValue());
        $this->assertEquals($datetime_in_utc, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));
    }

    public function testCreateValueDoesntAcceptStrings()
    {
        $datetime = '2014-12-30T13:45:55.123+01:00';
        $attribute = new TimestampAttribute('publishedAt', [ TimestampAttribute::OPTION_ACCEPT_STRINGS => false ]);
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
        $value->setValue($datetime);
        $this->assertEquals($attribute->getNullValue(), $value->getValue());
    }

    public function testCreateValueWithDefaultValueAsString()
    {
        $datetime = '2014-12-29T13:45:55.123+01:00';
        $datetime_in_utc = '2014-12-29T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute('publishedAt', [ TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime ]);
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertInstanceOf('\\DateTimeImmutable', $value->getValue());
        $this->assertEquals($datetime_in_utc, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));
    }

    public function testCreateValueWithDefaultValueAsStringWithoutDefaultTimezoneForcing()
    {
        $datetime = '2014-12-28T13:45:55.123+01:00';
        $datetime_in_cet = '2014-12-28T13:45:55.123000+01:00';
        $datetime_in_utc = '2014-12-28T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute(
            'publishedAt',
            [
                TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime,
                TimestampAttribute::OPTION_FORCE_INTERNAL_TIMEZONE => false
            ]
        );
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertInstanceOf('\\DateTimeImmutable', $value->getValue());
        $this->assertEquals($datetime_in_cet, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));
    }

    public function testDateTimeVsDateTimeImmutableValueComparison()
    {
        $datetime = '2014-12-28T13:45:55.123+01:00';
        $datetime_in_cet = '2014-12-28T13:45:55.123000+01:00';
        $datetime_in_utc = '2014-12-28T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute('publishedAt', [ TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime ]);
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertInstanceOf('\\DateTimeImmutable', $value->getValue());

        $dt1 = new DateTime($datetime);
        $dt2 = new DateTimeImmutable($datetime);

        $this->assertTrue($value->isEqualTo($dt1));
        $this->assertTrue($value->isEqualTo($dt2));
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new TimestampAttribute('publishedAt');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::CRITICAL, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(array()),
            array(new stdClass()),
            array(1)
        );
    }
}
