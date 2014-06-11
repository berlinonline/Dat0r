<?php

namespace Dat0r\CodeGen\Parser\TypeSchema;

use Dat0r\CodeGen\Parser\IParser;
use Dat0r\Common\Object;
use Dat0r\Common\Error\ParseException;
use Dat0r\Common\Error\FileSystemException;
use Dat0r\CodeGen\Schema\TypeSchema;
use DOMDocument;
use DOMXPath;

class TypeSchemaXmlParser extends Object implements IParser
{
    const BASE_DOCUMENT = '\Dat0r\Runtime\Document\Document';

    protected $xsd_schema_file;

    public function __construct()
    {
        $config_dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $path_parts = array($config_dir, 'config', 'type_schema.xsd');
        $this->xsd_schema_file = implode(DIRECTORY_SEPARATOR, $path_parts);
    }

    public function parse($schema_path, array $options = array())
    {
        $document = $this->createDomDocument($schema_path);
        $schema_root = $document->documentElement;
        $xpath = new DOMXPath($document);

        $type_definition_parser = TypeDefinitionXpathParser::create();
        $aggregates_parser = AggregateDefinitionXpathParser::create();
        $references_parser = ReferenceDefinitionXpathParser::create();
        $parse_options = array('context' => $schema_root);

        $self_uri = $schema_path;
        if (0 !== mb_strpos($schema_path, 'file://')) {
            $self_uri = 'file://' . $schema_path;
        }
        return TypeSchema::create(
            array(
                'self_uri' => $self_uri,
                'namespace' => $schema_root->getAttribute('namespace'),
                'package' => $schema_root->getAttribute('package'),
                'type_definition' => $type_definition_parser->parse($xpath, $parse_options),
                'aggregate_definitions' => $aggregates_parser->parse($xpath, $parse_options),
                'reference_definitions' => $references_parser->parse($xpath, $parse_options)
            )
        );
    }

    protected function createDomDocument($type_schema_file)
    {
        if (!is_readable($type_schema_file)) {
            throw new FileSystemException(
                "Unable to read file at path '$type_schema_file'."
            );
        }
        // @todo more xml error handling
        $document = new DOMDocument('1.0', 'utf-8');

        if (!$document->load($type_schema_file)) {
            throw new ParseException(
                "Failed loading the given type-schema."
            );
        }

        if (!$document->schemaValidate($this->xsd_schema_file)) {
            throw new ParseException(
                "Schema validation for the given type-schema failed."
            );
        }

        return $document;
    }
}
