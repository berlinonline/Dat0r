<?php

namespace Dat0r\Tests\Runtime\Attribute\Boolean;

use Dat0r\Runtime\Attribute\Boolean\BooleanAttribute;
use Dat0r\Runtime\Attribute\Boolean\BooleanValueHolder;
use Dat0r\Tests\TestCase;

class BooleanValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new BooleanAttribute('flag');
        $vh = new BooleanValueHolder($attribute);
        $this->assertEquals($attribute->getNullValue(), $vh->getValue());
    }

    public function testDefaultValue()
    {
        $attribute = new BooleanAttribute('flag', [ BooleanAttribute::OPTION_DEFAULT_VALUE => 'on' ]);
        $vh = $attribute->createValueHolder($attribute);
        $this->assertTrue($vh->getValue());
        $this->assertNotEquals($attribute->getNullValue(), $vh->getValue());
        $this->assertEquals($attribute->getDefaultValue(), $vh->getValue());
    }

    public function testToNative()
    {
        $attribute = new BooleanAttribute('flag', [ BooleanAttribute::OPTION_DEFAULT_VALUE => 'yes' ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertTrue($valueholder->toNative());

        $valueholder->setValue('');
        $this->assertFalse($valueholder->toNative());

        $valueholder->setValue('no');
        $this->assertFalse($valueholder->toNative());

        $valueholder->setValue(true);
        $this->assertTrue($valueholder->toNative());

        $valueholder->setValue('invalidvalue');
        $this->assertTrue($valueholder->toNative(), 'value should still be true as an invalid value was given.'); // still true
    }

    public function testToNativeRoundtripWithNullValue()
    {
        $attribute = new BooleanAttribute('flag');
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('on');
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals(true, $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($valueholder->toNative());
        $this->assertTrue($valueholder->getValue());
    }
}
