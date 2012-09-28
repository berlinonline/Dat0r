<?php

namespace CMF\Core\CodeGenerator;

class CodeCache
{
    private $config;

    public static function create(Configuration $configuration)
    {
        return new static($configuration);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getPath(ModuleDefinition $moduleDefinition)
    {
        $cacheDir = realpath($this->config->getCacheDir());
        $moduleName = $moduleDefinition->getName();
        return $cacheDir . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR;
    }

    public function has(ModuleDefinition $moduleDefinition)
    {
        $files = $this->read($moduleDefinition);
        return ! empty($files['base']) || ! empty($files['skeleton']);
    }

    public function read(ModuleDefinition $moduleDefinition)
    {
        $cacheDir = realpath($this->getConfig()->getCacheDir());
        if (! is_dir($cacheDir))
        {
            throw new \Exception(
                sprintf(
                    "Cache directory directory: %s does not exist but is required to do so.",
                    $this->getConfig()->getCacheDir()
                )
            );
        }

        $moduleName = $moduleDefinition->getName();
        $moduleDir = $cacheDir . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR;
        $baseClassDir = $moduleDir . 'base' . DIRECTORY_SEPARATOR;
        if (! is_readable($moduleDir) || ! is_readable($baseClassDir))
        {
            throw new \Exception("Unable to read from module cache directories: $moduleDir, $baseClassDir");
        }

        $cachedFiles = array('base' => array(), 'skeleton' => array());
        foreach (glob($moduleDir . 'base/*.class.php') as $baseFile)
        {
            $cachedFiles['base'][] = $baseFile;
        }
        foreach (glob($moduleDir . '*.class.php') as $skeletonFile)
        {
            $cachedFiles['skeleton'][] = $skeletonFile;
        }
        return $cachedFiles;
    }

    public function write(BuildResult $result)
    {
        $cacheDir = realpath($this->getConfig()->getCacheDir());
        if (! is_dir($cacheDir))
        {
            throw new \Exception(
                sprintf(
                    "Cache directory directory: %s does not exist but is required to do so.",
                    $this->getConfig()->getCacheDir()
                )
            );
        }
        $moduleName = $result->getModuleDefinition()->getName();
        $moduleDir = $cacheDir . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR;
        $baseClassDir = $moduleDir . 'base' . DIRECTORY_SEPARATOR;
        if (! is_dir($moduleDir))
        {
            if (! mkdir($moduleDir))
            {
                throw new \Exception("Unable to create cache directory: $moduleDir");
            }
        }
        if (! is_dir($baseClassDir))
        {
            if (! mkdir($baseClassDir))
            {
                throw new \Exception("Unable to create cache directory: $baseClassDir");
            }
        }
        if (! is_writable($moduleDir) || ! is_writable($baseClassDir))
        {
            throw new \Exception("Unable to write module to cache directories: $moduleDir, $baseClassDir");
        }

        $tempFiles = array('base' => array(), 'skeleton' => array());
        foreach ($result->getBaseCode() as $codeDef)
        {
            $tempFiles['base'][] = $this->writeCacheFile($baseClassDir, $codeDef);
        }
        foreach ($result->getSkeletonCode() as $codeDef)
        {
            $tempFiles['skeleton'][] = $this->writeCacheFile($moduleDir, $codeDef);
        }
        return $tempFiles;
    }

    protected function writeCacheFile($baseDir, array $codeDef)
    {
        $filePath = $baseDir . $codeDef['class'] . '.class.php';
        if (! file_put_contents($filePath, $codeDef['source']))
        {
            throw new \Exception("Failed to write file: " . $filePath);
        }
        return $filePath;
    }


    protected function __construct(Configuration $configuration)
    {
        $this->config = $configuration;
    }
}
