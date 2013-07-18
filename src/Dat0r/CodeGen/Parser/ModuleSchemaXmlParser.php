<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class ModuleSchemaXmlParser implements IModuleSchemaParser
{
    const BASE_DOCUMENT = '\Dat0r\Core\Document\Document';

    protected $xsd_schema_file;

    public function parseSchema($schema_path)
    {
        $document = $this->createDomDocument($schema_path);
        $module_schema_element = $document->documentElement;
        $xpath = new \DOMXPath($document);

        $module_definition_parser = new ModuleDefinitionXpathParser();
        $aggregates_parser = new AggregateDefinitionXpathParser();

        return Schema\ModuleSchema::create(array(
            'namespace' => $module_schema_element->getAttribute('namespace'),
            'module_definition' => $module_definition_parser->parseXpath(
                $xpath,
                array('context' => $module_schema_element)
            ),
            'aggregate_definitions' => $aggregates_parser->parseXpath(
                $xpath,
                array('context' => $module_schema_element)
            )
        ));
    }

    public static function create()
    {
        return new static();
    }

    protected function createDomDocument($module_schema_file)
    {
        if (!is_readable($module_schema_file))
        {
            throw new ParseException(
                "Unable to read file at path '$module_schema_file'."
            );
        }

        $document = new \DOMDocument('1.0', 'utf-8');

        if (!$document->load($module_schema_file))
        {
            throw new ParseException(
                "Failed loading the given module-schema."
            );
        }

        if (!$document->schemaValidate($this->xsd_schema_file))
        {
            throw new ParseException(
                "Schema validation for the given module-schema failed."
            );
        }

        return $document;
    }

    protected function __construct()
    {
        $config_dir = dirname(dirname(dirname(dirname(__DIR__))));
        $path_parts = array($config_dir, 'config', 'module_schema.xsd');
        $this->xsd_schema_file = implode(DIRECTORY_SEPARATOR, $path_parts);
    }
}
