<?php

namespace Dat0r\Tests\CodeGen\Parser;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Schema\TypeSchema;
use Dat0r\CodeGen\Schema\OptionDefinition;
use Dat0r\CodeGen\Parser\TypeSchema\TypeSchemaXmlParser;

class TypeSchemaTest extends TestCase
{
    public function testGetUsedAggregateDefinitions()
    {
        $schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';

        $schema_parser = new TypeSchemaXmlParser();
        $type_schema = $schema_parser->parse($schema_path);

        $aggregate_defs = $type_schema->getUsedAggregateDefinitions(
            $type_schema->getTypeDefinition()
        );

        $this->assertEquals(1, $aggregate_defs->getSize());
    }

    public function testGetUsedReferenceDefinitions()
    {
        $schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';

        $schema_parser = new TypeSchemaXmlParser();
        $type_schema = $schema_parser->parse($schema_path);

        $aggregate_defs = $type_schema->getUsedReferenceDefinitions(
            $type_schema->getTypeDefinition()
        );

        $this->assertEquals(2, $aggregate_defs->getSize());
    }
}
