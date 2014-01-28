<?php

namespace Dat0r\CodeGen\Parser\ModuleSchema;

use Dat0r\CodeGen\Parser\IParser;
use Dat0r\Common\Object;
use Dat0r\Common\Error\ParseException;
use Dat0r\Common\Error\FilesystemException;
use Dat0r\CodeGen\Schema\ModuleSchema;
use DOMDocument;
use DOMXPath;

class ModuleSchemaXmlParser extends Object implements IParser
{
    const BASE_DOCUMENT = '\Dat0r\Runtime\Document\Document';

    protected $xsd_schema_file;

    public function __construct()
    {
        $config_dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $path_parts = array($config_dir, 'config', 'module_schema.xsd');
        $this->xsd_schema_file = implode(DIRECTORY_SEPARATOR, $path_parts);
    }

    public function parse($schema_path, array $options = array())
    {
        $document = $this->createDomDocument($schema_path);
        $schema_root = $document->documentElement;
        $xpath = new DOMXPath($document);

        $module_definition_parser = ModuleDefinitionXpathParser::create();
        $aggregates_parser = AggregateDefinitionXpathParser::create();
        $references_parser = ReferenceDefinitionXpathParser::create();
        $parse_options = array('context' => $schema_root);

        $self_uri = $schema_path;
        if (0 !== mb_strpos($schema_path, 'file://')) {
            $self_uri = 'file://' . $schema_path;
        }
        return ModuleSchema::create(
            array(
                'self_uri' => $self_uri,
                'namespace' => $schema_root->getAttribute('namespace'),
                'package' => $schema_root->getAttribute('package'),
                'module_definition' => $module_definition_parser->parse($xpath, $parse_options),
                'aggregate_definitions' => $aggregates_parser->parse($xpath, $parse_options),
                'reference_definitions' => $references_parser->parse($xpath, $parse_options)
            )
        );
    }

    protected function createDomDocument($module_schema_file)
    {
        if (!is_readable($module_schema_file)) {
            throw new FilesystemException(
                "Unable to read file at path '$module_schema_file'."
            );
        }
        // @todo more xml error handling
        $document = new DOMDocument('1.0', 'utf-8');

        if (!$document->load($module_schema_file)) {
            throw new ParseException(
                "Failed loading the given module-schema."
            );
        }

        if (!$document->schemaValidate($this->xsd_schema_file)) {
            throw new ParseException(
                "Schema validation for the given module-schema failed."
            );
        }

        return $document;
    }
}
