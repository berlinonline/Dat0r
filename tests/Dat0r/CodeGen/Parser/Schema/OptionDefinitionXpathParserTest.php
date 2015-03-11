<?php

namespace Dat0r\Tests\CodeGen\Parser\Schema;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Parser\Schema\OptionDefinitionXpathParser;
use Dat0r\CodeGen\Parser\Schema\Xpath;

class OptionDefinitionXpathParserTest extends TestCase
{
    public function testOneNestedOptions()
    {
        $dom_document = new \DOMDocument('1.0', 'utf-8');
        $dom_document->loadXML(
            '<random_container>
                <option name="types">
                    <option>VotingStats</option>
                </option>
            </random_container>'
        );

        $xpath = new Xpath($dom_document);
        $parser = new OptionDefinitionXpathParser();
        $option_definitions = $parser->parse(
            $xpath,
            array('context' => $dom_document->documentElement)
        );

        $types_option = $option_definitions[0];
        $types_options_value = $types_option->getValue();

        $this->assertInstanceOf(
            'Dat0r\CodeGen\Schema\OptionDefinitionList',
            $option_definitions
        );
        $this->assertInstanceOf(
            'Dat0r\CodeGen\Schema\OptionDefinitionList',
            $types_option->getValue()
        );
        $this->assertEquals(1, $option_definitions->getSize());
        $this->assertEquals('VotingStats', $types_options_value[0]->getValue());
    }
}
