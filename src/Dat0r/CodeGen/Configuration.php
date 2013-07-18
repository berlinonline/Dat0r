<?php

namespace Dat0r\CodeGen;

class Configuration
{
    private $basePath;

    private $cacheDir;

    private $templateDir;

    private $deployDir;

    private $deployMethod;

    private $plugins = array();

    public static function create(array $config)
    {
        return new static($config);
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function getBasePath()
    {
        return $this->basePath;
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

    public function getPlugin($pluginName)
    {
        if (isset($this->plugins[$pluginName]))
        {
            return $this->plugns[$pluginName];
        }

        throw new Exception("The given plugin $pluginName does not exist.");
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    protected function __construct(array $config)
    {
        $keys = array('cacheDir', 'deployDir', 'deployMethod', 'basePath');
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

        $this->registerPlugins($config);
    }

    protected function registerPlugins(array $config)
    {
        if (! isset($config['plugins']) || ! is_array($config['plugins']))
        {
            return;
        }

        foreach ($config['plugins'] as $class => $classPath)
        {
            if (! is_readable($classPath))
            {
                throw new \Exception("Unable to read plugin at location $classPath");
            }

            require $classPath;

            $pluginConfig = isset($config[$class]) ? $config[$class] : array();
            $pluginConfig['basePath'] = $this->getBasePath();
            $this->plugins[$class] = new $class($pluginConfig);
        }
    }

    public static function normalizePath($path) 
    {
        return array_reduce(
            explode(DIRECTORY_SEPARATOR, $path), function($a, $b) {
                if(0 === $a)
                {
                    $a = DIRECTORY_SEPARATOR;
                }
                if("" === $b || "." === $b)
                {
                    return $a;
                }
                if(".." === $b)
                {
                    return dirname($a);
                }
                return preg_replace(
                    sprintf("/\%s+/", DIRECTORY_SEPARATOR), 
                    DIRECTORY_SEPARATOR, 
                    $a.DIRECTORY_SEPARATOR.$b
                );
            }, 0
        );
    }
}
