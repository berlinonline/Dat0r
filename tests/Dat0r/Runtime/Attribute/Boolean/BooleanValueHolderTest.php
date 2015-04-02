<?php

namespace Dat0r\Tests\Runtime\Attribute\Boolean;

use Dat0r\Runtime\Attribute\Boolean\BooleanAttribute;
use Dat0r\Runtime\Attribute\Boolean\BooleanValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
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
        $vh = $attribute->createValueHolder(true);
        $this->assertTrue($vh->getValue());
        $this->assertNotEquals($attribute->getNullValue(), $vh->getValue());
        $this->assertEquals($attribute->getDefaultValue(), $vh->getValue());
    }

    public function testToNative()
    {
        $attribute = new BooleanAttribute('flag', [ BooleanAttribute::OPTION_DEFAULT_VALUE => 'yes' ]);
        $valueholder = $attribute->createValueHolder(true);

        $this->assertTrue($valueholder->toNative());

        $attribute = new BooleanAttribute('flag', [ BooleanAttribute::OPTION_DEFAULT_VALUE => 'no' ]);
        $result = $valueholder->setValue(''); // interpreted as FALSE
        $this->assertTrue($result->getSeverity() === IncidentInterface::SUCCESS);

        $valueholder->setValue('no');
        $this->assertFalse($valueholder->toNative());

        $valueholder->setValue(true);
        $this->assertTrue($valueholder->toNative());

        $result = $valueholder->setValue('invalidvalue');
        $this->assertTrue($valueholder->toNative(), 'value should still be true as an invalid value was given.');
        $this->assertTrue($result->getSeverity() > IncidentInterface::SUCCESS);
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
