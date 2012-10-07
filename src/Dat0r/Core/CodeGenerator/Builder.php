<?php

namespace Dat0r\Core\CodeGenerator;

class Builder
{
    private $config;

    private $generator;

    public static function create(Configuration $config)
    {
        return new static($config);
    }

    public function build(ModuleDefinition $moduleDefinition)
    {
        $this->generator->setModuleDefinition($moduleDefinition);
        $moduleName = $moduleDefinition->getName();
        $code = array(
            'base' => array($this->generator->generateBaseModule(), $this->generator->generateBaseDocument()),
            'skeleton' => array($this->generator->generateModuleSkeleton(), $this->generator->generateDocumentSkeleton())
        );

        // @todo not run atm, will probally work as soon as parsing aggregates is supported.
        foreach ($moduleDefinition->getAggregates() as $aggregateDefinition)
        {
            $this->generator->setModuleDefinition($aggregateDefinition);
            $moduleName = $moduleDefinition->getName();
            $code['base'] = array_merge($code['base'],
                array($this->generator->generateBaseModule(), $this->generator->generateBaseDocument())
            );
            $code['skeleton'] = array_merge($code['skeleton'], 
                array($this->generator->generateModuleSkeleton(), $this->generator->generateDocumentSkeleton())
            );
        }

        $cache = CodeCache::create($this->getConfig());
        $cache->write(BuildResult::create($moduleDefinition, $code));
    }

    protected function __construct(Configuration $config) 
    {
        $this->config = $config;
        $this->generator = CodeGenerator::create($this->config);
    }

    protected function getConfig()
    {
        return $this->config;
    }
}
