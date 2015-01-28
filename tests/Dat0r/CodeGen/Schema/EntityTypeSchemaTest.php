<?php

namespace Dat0r\Tests\CodeGen\Parser;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Schema\EntityTypeSchema;
use Dat0r\CodeGen\Schema\OptionDefinition;
use Dat0r\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;

class EntityTypeSchemaTest extends TestCase
{
    public function testGetUsedEmbedDefinitions()
    {
        $schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';

        $schema_parser = new EntityTypeSchemaXmlParser();
        $type_schema = $schema_parser->parse($schema_path);

        $embed_defs = $type_schema->getUsedEmbedDefinitions(
            $type_schema->getEntityTypeDefinition()
        );

        $this->assertEquals(1, $embed_defs->getSize());
    }

    public function testGetUsedReferenceDefinitions()
    {
        $schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';

        $schema_parser = new EntityTypeSchemaXmlParser();
        $type_schema = $schema_parser->parse($schema_path);

        $embed_defs = $type_schema->getUsedReferenceDefinitions(
            $type_schema->getEntityTypeDefinition()
        );

        $this->assertEquals(2, $embed_defs->getSize());
    }
}
