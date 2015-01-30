<?php

namespace Dat0r\Tests\Runtime\Attribute\Timestamp;

use Dat0r\Runtime\Attribute\Timestamp\TimestampAttribute;
use Dat0r\Runtime\Attribute\Timestamp\TimestampValueHolder;
use Dat0r\Tests\TestCase;

class TimestampValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new TimestampAttribute('publishedAt');
        $vh = new TimestampValueHolder($attribute);
        $this->assertEquals($attribute->getNullValue(), $vh->getValue());

        $attribute = new TimestampAttribute('publishedAt', [ TimestampAttribute::OPTION_DEFAULT_VALUE => 'now' ]);
        $vh = $attribute->createValueHolder($attribute);
        $this->assertNotEquals($attribute->getNullValue(), $vh->getValue());
    }

    public function testToNative()
    {
        $datetime = '2014-12-27T12:34:56.789123+01:00';
        $datetime_string = '2014-12-27T11:34:56.789123+00:00';
        $attribute = new TimestampAttribute('birthday', [ TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals($datetime_string, $valueholder->toNative());
    }

    public function testToNativeRoundtripWithNullValue()
    {
        $attribute = new TimestampAttribute('birthday');
        $valueholder = $attribute->createValueHolder();
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals('', $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }
}
