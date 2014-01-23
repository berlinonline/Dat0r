<?php

namespace Dat0r\Tests\CodeGen\Parser\ModuleSchema;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Parser\ModuleSchema\OptionDefinitionXpathParser;
use DOMXPath;

class OptionDefinitionXpathParserTest extends TestCase
{
    public function testOneNestedOptions()
    {
        $dom_document = new \DOMDocument('1.0', 'utf-8');
        $dom_document->loadXML(
            '<random_container>
                <option name="modules">
                    <option>VotingStats</option>
                </option>
            </random_container>'
        );

        $xpath = new DOMXPath($dom_document);
        $parser = OptionDefinitionXpathParser::create();
        $option_definitions = $parser->parse(
            $xpath,
            array('context' => $dom_document->documentElement)
        );

        $modules_option = $option_definitions[0];
        $modules_options_value = $modules_option->getValue();

        $this->assertInstanceOf(
            'Dat0r\CodeGen\Schema\OptionDefinitionList',
            $option_definitions
        );
        $this->assertInstanceOf(
            'Dat0r\CodeGen\Schema\OptionDefinitionList',
            $modules_option->getValue()
        );
        $this->assertEquals(1, $option_definitions->getSize());
        $this->assertEquals('VotingStats', $modules_options_value[0]->getValue());
    }
}
