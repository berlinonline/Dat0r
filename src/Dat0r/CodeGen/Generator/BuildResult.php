<?php

namespace Dat0r\CodeGen\CodeGenerator;

class BuildResult
{
    private $baseCode;

    private $skeletonCode;

    private $moduleDefinition;

    public static function create(ModuleDefinition $moduleDefinition, array $generatedCode)
    {
        foreach (array('base', 'skeleton') as $requiredKey)
        {
            if (! isset($generatedCode[$requiredKey]))
            {
                throw new \Exception("Missing required key '$requiredKey' on incoming data.");
            }
        }
        return new static($moduleDefinition, $generatedCode);
    }

    public function getBaseCode()
    {
        return $this->baseCode;
    }

    public function getSkeletonCode()
    {
        return $this->skeletonCode;
    }

    public function getModuleDefinition()
    {
        return $this->moduleDefinition;
    }

    protected function __construct(ModuleDefinition $moduleDefinition, array $generatedCode)
    {
        $this->moduleDefinition = $moduleDefinition;
        $this->baseCode = $generatedCode['base'];
        $this->skeletonCode = $generatedCode['skeleton'];
    }
}
