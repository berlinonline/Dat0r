<?php

namespace Dat0r\Tests\CodeGen\Parser\ModuleSchema;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Parser\ModuleSchema\ModuleSchemaXmlParser;

class ModuleSchemaXmlParserTest extends TestCase
{
    public function testParseSchema()
    {
        $module_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'extensive_module_example.xml';

        $parser = ModuleSchemaXmlParser::create();
        $module_schema = $parser->parse($module_schema_path);

        $this->assertInstanceOf('\Dat0r\CodeGen\Schema\ModuleSchema', $module_schema);
    }
}
