<?php

namespace Dat0r\Tests\CodeGen\Parser\Schema;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;

class EntityTypeSchemaXmlParserTest extends TestCase
{
    public function testParseSchema()
    {
        $type_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'extensive_type_schema.xml';

        $parser = new EntityTypeSchemaXmlParser();
        $type_schema = $parser->parse($type_schema_path);

        $this->assertInstanceOf('\Dat0r\CodeGen\Schema\EntityTypeSchema', $type_schema);
    }
}
