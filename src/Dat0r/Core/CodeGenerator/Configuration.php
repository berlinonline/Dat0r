<?php

namespace Dat0r\Core\CodeGenerator;

// @todo Read settings from a config file.
class Configuration
{
    private $cacheDir;

    private $templateDir;

    private $deployDir;

    private $deployMethod;

    public static function create(array $config)
    {
        return new static($config);
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    public function getDeployDir()
    {
        return $this->deployDir;
    }

    public function getDeployMethod()
    {
        return $this->deployMethod;
    }

    protected function __construct(array $config)
    {
        $keys = array('cacheDir', 'deployDir', 'deployMethod');
        foreach ($keys as $key)
        {
            if (! isset($config[$key]))
            {
                throw new \Exception("Missing '$key' config setting.");
            }
            $this->$key = $config[$key];
        }

        if (! $this->templateDir)
        {
            $this->templateDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates';
        }
    }
}
