<?php

namespace CMF\Core\CodeGenerator;

class CodeGenerator                                                                                                                                             
{
    const NS_MODULE = '\CMF\Core\Runtime\Module';

    const NS_FIELDS = '\CMF\Core\Runtime\Field';

    const NS_DOCUMENT = '\CMF\Core\Runtime\Document';

    const DOMAIN_BASE_NS = 'CMF\Domain\Runtime';

    protected $config;

    protected $twig;

    protected $moduleDefinition;

    public static function create(Configuration $config)
    {
        return new static($config);
    }

    public function setModuleDefinition(ModuleDefinition $moduleDefinition)
    {
        $this->moduleDefinition = $moduleDefinition;
    }

    public function generateBaseModule()
    {
        $moduleName = $this->moduleDefinition->getName();
        $implementor = sprintf('Base%sModule', $moduleName);
        $namespace = $this->buildNamespace();
        $parentClass = sprintf('%s\%sModule', self::NS_MODULE, ucfirst($this->moduleDefinition->getType()));

        $source = $this->twig->render('Module/BaseModule.twig', array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $moduleName,
            'namespace' => $namespace,
            'parent_class' => $parentClass,
            'implementor' => $implementor,
            'fields' => $this->prepareFieldDefinitions(),
            'document_implementor' => sprintf('%s\%sDocument', $namespace, $moduleName),
            'options' => $this->moduleDefinition->getOptions()
        ));

        return array('class' => $implementor, 'source' => $source);
    }

    public function generateModuleSkeleton()
    {
        $moduleName = $this->moduleDefinition->getName();
        $implementor = sprintf('%sModule', $moduleName);
        $namespace = $this->buildNamespace();

        $source = $this->twig->render('Module/Module.twig', array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $moduleName,
            'namespace' => $namespace,
            'parent_class' => sprintf('Base%sModule', $moduleName),
            'implementor' => $implementor
        ));

        return array('class' => $implementor, 'source' => $source);
    }

    public function generateBaseDocument()
    {
        $moduleName = $this->moduleDefinition->getName();
        $implementor = sprintf('Base%sDocument', $moduleName);
        $namespace = $this->buildNamespace();

        $source = $this->twig->render('Document/BaseDocument.twig', array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $moduleName,
            'namespace' => $namespace,
            'parent_class' => $this->moduleDefinition->getBase(),
            'implementor' => $implementor,
            'fields' => $this->prepareFieldDefinitions(),
            'document_implementor' => sprintf('%s\%sDocument', $namespace, $moduleName),
            'options' => $this->moduleDefinition->getOptions()
        ));

        return array('class' => $implementor, 'source' => $source);
    }

    public function generateDocumentSkeleton()
    {
        $moduleName = $this->moduleDefinition->getName();
        $implementor = sprintf('%sDocument', $moduleName);
        $namespace = $this->buildNamespace();

        $source = $this->twig->render('Document/Document.twig', array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $moduleName,
            'namespace' => $namespace,
            'parent_class' => sprintf('Base%sDocument', $moduleName),
            'implementor' => $implementor,
            'description' => $this->moduleDefinition->getDescription()
        ));

        return array('class' => $implementor, 'source' => $source);
    }

    protected function __construct(Configuration $config) 
    {
        $this->config = $config;
        $loader = new \Twig_Loader_Filesystem($this->config->getTemplateDir());
        $this->twig = new \Twig_Environment($loader);
    }

    protected function prepareFieldDefinitions()
    {
        $fields = array();
        foreach ($this->moduleDefinition->getFields() as $field)
        {
            $field['implementor'] = sprintf('%s\%sField', self::NS_FIELDS, ucfirst($field['type']));
            $field['options'] = $this->prepareOptions($field['options']);
            $fields[] = $field;
        }
        return $fields;
    }

    protected function prepareOptions(array $options)
    {
        $prepared = array();
        foreach ($options as $name => $value)
        {
            $formatted = preg_replace(
                array('/array\s*\(\s*/is', '/,\s+/is', '/\d+\s+=>\s+/is', '/\s+=>\s+/is'),
                array("array(", ', ', '', ' => '),
                preg_replace('/\n/is', '', var_export($value, TRUE))
            );
            $prepared[$name] = array('name' => $name, 'value' => $formatted);
        }
        return $prepared;
    }

    protected function buildNamespace()
    {
        $package = $this->moduleDefinition->getPackage();
        $moduleName = $this->moduleDefinition->getRoot();
        $parts = explode('/', $package);
        array_shift($parts);

        if (1 > count($parts))
        {
            throw new \Exception(
                "Invalid package value given. Packages must start with / and end with a letter."
            );
        }

        $lastPart = array_pop($parts);
        array_unshift($parts, $moduleName);
        
        return sprintf('%s\%s', self::DOMAIN_BASE_NS, implode('\\', $parts) . $lastPart);
    }
}
