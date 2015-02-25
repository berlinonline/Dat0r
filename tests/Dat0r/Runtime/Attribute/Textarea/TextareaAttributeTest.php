<?php

namespace Dat0r\Tests\Runtime\Attribute\Textarea;

use Dat0r\Runtime\Attribute\Textarea\TextareaAttribute;
use Dat0r\Runtime\Attribute\Textarea\TextareaValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;

class TextareaAttributeTest extends TestCase
{
    const FIELDNAME = 'textarea_attribute';

    public function testCreate()
    {
        $text_attribute = new TextareaAttribute(self::FIELDNAME);
        $this->assertEquals($text_attribute->getName(), self::FIELDNAME);
    }

    public function testDefaultTabAndNewlineNormalizationBehaviour()
    {
        $string = "\n CHA\x00RSET - WÄHLE \t\t\r\nUTF-8 AS SENSIBLE DEFAULT!  ";
        $string_trimmed = "CHARSET - WÄHLE \t\t\r\nUTF-8 AS SENSIBLE DEFAULT!";
        $attribute = new TextareaAttribute(self::FIELDNAME);
        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue($string);
        $this->assertTrue($string_trimmed === $valueholder->getValue());
    }
}