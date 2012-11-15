<?php

namespace Dat0r\Core\CodeGenerator;

// @todo Read settings from a config file.
class Deployment
{
    const COPY_DEPLOYMENT = 'copy';

    const MOVE_DEPLOYMENT = 'move';

    private $config;

    private $cache;

    public static function create(Configuration $config)
    {
        return new static($config);
    }

    public function deploy(ModuleDefinition $moduleDefinition)
    {
        $deployDir = $this->config->getDeployDir();
        $cacheDir = $this->cache->getPath($moduleDefinition);
        $targetDir = $deployDir . DIRECTORY_SEPARATOR . $moduleDefinition->getName();

        if ($this->cache->has($moduleDefinition))
        {
            $sourceFiles = $this->cache->read($moduleDefinition);
            $this->checkDirectories($targetDir);

            if (self::MOVE_DEPLOYMENT === $this->config->getDeployMethod())
            {
                $this->moveCache($sourceFiles, $cacheDir, $targetDir);
            }
            else
            {
                $this->copyCache($sourceFiles, $cacheDir, $targetDir);
            }
        }
        else
        {
            throw new \Exception("No cache to deploy for the given module definition.");
        }
    }

    protected function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->cache = $codeCache = CodeCache::create($this->config);
    }

    protected function moveCache(array $sourceFiles, $cacheDir, $targetDir)
    {
        $cacheBaseDir = realpath($cacheDir . '/Base');
        $targetBaseDir = realpath($targetDir . '/Base');
        foreach ($sourceFiles['base'] as $base)
        {
            $fileName = basename($base);
            rename($base, sprintf('%s/%s', $targetBaseDir, $fileName));
        }
        @rmdir($cacheBaseDir); // try to clean up
        foreach ($sourceFiles['skeleton'] as $skeleton)
        {
            $fileName = basename($skeleton);
            rename($skeleton, sprintf('%s/%s', $targetDir, $fileName));
        }
        @rmdir($cacheDir); // try to clean up
    }

    protected function copyCache(array $sourceFiles, $cacheDir, $targetDir)
    {
        $cacheBaseDir = realpath($cacheDir . '/Base');
        $targetBaseDir = realpath($targetDir . '/Base');
        foreach ($sourceFiles['base'] as $base)
        {
            $fileName = basename($base);
            copy($base, sprintf('%s/%s', $targetBaseDir, $fileName));
        }
        foreach ($sourceFiles['skeleton'] as $skeleton)
        {
            $fileName = basename($skeleton);
            copy($skeleton, sprintf('%s/%s', $targetDir, $fileName));
        }
    }

    protected function checkDirectories($targetDir)
    {
        if (! is_dir($targetDir))
        {
            if (! mkdir($targetDir, 0775, TRUE))
            {
                throw new \Exception("Can't create deploy directory: $deployDir/".$moduleDefinition->getName());
            }
        }
        if (! is_writable($targetDir))
        {
            throw new \Exception("Can write to deploy directory: $deployDir/".$moduleDefinition->getName());
        }

        $targetBaseDir = $targetDir . '/Base';
        if (! is_dir($targetBaseDir) && ! mkdir($targetBaseDir, 0775, TRUE))
        {
            throw new \Exception("Can't create deploy directory: $deployDir/".$moduleDefinition->getName().'/Base');
        }
        if (! is_writable($targetBaseDir))
        {
            throw new \Exception("Can write to deploy directory: $deployDir/".$moduleDefinition->getName().'/Base');
        }
    }
}
