<?php

namespace CMF\Core\CodeGenerator;

class ModuleDefinitionParser                                                                                                                                          
{
    const BASE_DOCUMENT = '\CMF\Core\Runtime\Document\Document';

    public static function create()
    {
        return new static();
    }
 
    public function parse($filePath)
    {   
        if (! is_readable($filePath))
        {
            throw new ParseException("Unable to read file at path '$filePath'.");
        }

        $document = new \DOMDocument('1.0', 'utf-8');
        $document->load($filePath);
        $xpath = new \DOMXPath($document);

        $moduleDefinition = ModuleDefinition::create(
            $this->parseModuleElement($xpath->query('/module')->item(0), $xpath)
        );
        
        return $moduleDefinition;
    }

    protected function __construct() {}

    protected function parseModuleElement(\DOMElement $element, \DOMXPath $xpath, array $rootData = NULL)
    {
        // @todo validate name (must start uppercase, letters & numbers only), package, desc and fields.
        $name = $element->getAttribute('name');
        $namespace = $element->getAttribute('namespace');
        $package = $this->parsePackageAttribute($element);
        $description = $this->parseDescriptionElement(
            $xpath->query('description', $element)->item(0)
        );
        $root = empty($rootData) ? $name : $rootData['name'];
        $type = empty($rootData) ? 'root' : 'aggregate';
        $package = empty($rootData) ? $package : $rootData['package'];
        $namespace = empty($rootData) ? $namespace : $rootData['namespace'];
        $options = $this->parseOptions($element, $xpath);

        $fields = array();
        foreach ($xpath->query('fields/field', $element) as $fieldElement)
        {
            $fieldData = $this->parseFieldElement($fieldElement, $xpath);
            if ('aggregate' === $fieldData['type'])
            {
                // @todo make module namespace configurable.
                $fieldData['options']['aggregate_module'] = sprintf(
                    '%s\%s\%s', $namespace, $root, $fieldData['options']['aggregate_module']
                );
            }
            $fields[$fieldData['name']] = $fieldData;
        }

        $moduleData = array(
            'type' => $type,
            'package' => $package,
            'namespace' => $namespace,
            'root' => $root,
            'base' => self::BASE_DOCUMENT, // @todo allow to configure the base?
            'name' => $name,
            'description' => $description,
            'options' => $options,
            'fields' => $fields,
            'aggregates' => array()
        );

        if (empty($rootData))
        {
            $aggregates = array();
            foreach ($xpath->query('//aggregate') as $aggregateElement)
            {
                $aggregateData = $this->parseModuleElement($aggregateElement, $xpath, $moduleData);
                $aggregate = ModuleDefinition::create($aggregateData);
                $aggregates[$aggregate->getName()] = $aggregate;
            }
            $moduleData['aggregates'] = $aggregates;
        }

        return $moduleData;
    }

    protected function parseFieldElement(\DOMElement $element, \DOMXPath $xpath)
    {
        // @todo need to find the correct place for these mappings. maybe define it in xml.
        $phpTypeMap = array(
            'text' => 'string', 
            'integer' => 'int', 
            'struct' => 'array',
            'aggregate' => 'object'
        ); 
        // @todo validate field type (must resolve to valid class), name (letters/numbers only) and desc.
        $type = $element->getAttribute('type');
        $name = $element->getAttribute('name');
        $options = $options = $this->parseOptions($element, $xpath);
        $description = $this->parseDescriptionElement(
            $xpath->query('description', $element)->item(0)
        );

        if (! isset($phpTypeMap[$type]))
        {
            throw new \Exception("Unable to map field type '$type' to php-type.");
        }
        $phpType = $phpTypeMap[$type];
        
        $aggregateData = NULL;
        if ('aggregate' === $type)
        {
            $aggregateElement = $element->getElementsByTagName('aggregate')->item(0);
            $aggregateName = $aggregateElement->getAttribute('name') . 'Module';
            $options['aggregate_module'] = $aggregateName;
            $phpType = $aggregateName;
        }
        
        return array(
            'type' => $type,
            'php_type' => $phpType,
            'name' => $name,
            'description' => $description,
            'options' => $options
        );
    }

    protected function parseOptions(\DOMElement $element, \DOMXPath $xpath)
    {
        $options = array();
        $elementsToParse = array();

        if (($optionsElement = $xpath->query('options', $element)->item(0)))
        {
            $elementsToParse = $xpath->query('option', $optionsElement);
        }
        else
        {
            $elementsToParse = $xpath->query('option', $element);
        }

        foreach ($elementsToParse as $optionElement)
        {
            $name = $optionElement->getAttribute('name');
            $nestedOptions = $this->parseOptions($optionElement, $xpath);
            $value = empty($nestedOptions) ? trim($optionElement->nodeValue) : $nestedOptions;

            if (! empty($value))
            {
                if (empty($name))
                {
                    $options[] = $value;
                }
                else
                {
                    $options[$name] = $value;
                }
            }
            else
            {
                throw new ParseException("Missing value for option $name");
            }
        }

        return $options;
    }

    protected function parsePackageAttribute(\DOMElement $element)
    {
        $package = $element->getAttribute('package');
        if (! $package)
        {
            $package = '/';
        }
        if (substr($package, -1) !== '/')
        {
            $package .= '/';
        }
        if (substr($package, 0, 1) !== '/')
        {
            $package = '/' . $package;
        }

        return $package;
    }

    protected function parseDescriptionElement(\DOMElement $element)
    {
        return array_map(function($line)
        {
            return trim($line);
        }, preg_split ('/$\R?^/m', trim($element->nodeValue)));
    }
}
