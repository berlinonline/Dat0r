<?php

namespace Dat0r\CodeGen;

use Dat0r;

class Config extends Dat0r\Object
{
    protected $cache_dir;

    protected $deploy_dir;

    protected $plugin_settings;

    public function getCacheDir()
    {
        return $this->cache_dir;
    }

    public function getDeployDir()
    {
        return $this->deploy_dir;
    }

    public function getPluginSettings()
    {
        return $this->plugin_settings;
    }

    public function validate()
    {
        if (!is_dir($cache_dir))
        {
            throw new Exception("Invalid 'cache_dir' setting given to config.");
        }

        if (!is_dir($deploy_dir))
        {
            throw new Exception("Invalid 'deploy_dir' setting given to config.");
        }
    }
}
