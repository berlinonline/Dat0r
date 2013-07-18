<?php

namespace Dat0r\Tests\CodeGen\Parser;

use Dat0r\Tests\Core\BaseTest;
use Dat0r\CodeGen\Parser\ModuleSchemaXmlParser;

class ModuleSchemaXmlParserTest extends BaseTest
{

    public function testParseSchema()
    {
        $schema_path = dirname(dirname(dirname(dirname(__DIR__))))
            . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'topic_schema.xml';

        $parser = ModuleSchemaXmlParser::create();
        $module_schema = $parser->parseSchema($schema_path);

        $this->assertInstanceOf('\Dat0r\CodeGen\Schema\ModuleSchema', $module_schema);
    }
}
