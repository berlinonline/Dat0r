<?php

namespace Dat0r\Tests\CodeGen\Parser;

use Dat0r\Tests;
use Dat0r\CodeGen\Parser\ModuleSchemaXmlParser;

class ModuleSchemaXmlParserTest extends Tests\TestCase
{
    public function testParseSchema()
    {
        $module_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'extensive_module_example.xml';

        $parser = ModuleSchemaXmlParser::create();
        $module_schema = $parser->parseSchema($module_schema_path);

        $this->assertInstanceOf('\Dat0r\CodeGen\Schema\ModuleSchema', $module_schema);
    }
}
