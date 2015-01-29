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
}
