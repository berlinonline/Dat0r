<?php

namespace Dat0r\CodeGen;

use Dat0r;

class Config extends Dat0r\Object
{
    const DEPLOY_COPY = 'copy';

    const DEPLOY_MOVE = 'move';

    protected $cache_dir;

    protected $deploy_dir;

    protected $deploy_method = self::DEPLOY_COPY;

    protected $plugin_settings;

    public function getCacheDir()
    {
        return $this->cache_dir;
    }

    protected function setCacheDir($cache_dir)
    {
        $this->cache_dir = trim($cache_dir);
    }

    public function getDeployDir()
    {
        return $this->deploy_dir;
    }

    protected function setDeployDir($deploy_dir)
    {
        $this->deploy_dir = trim($deploy_dir);
    }

    public function getDeployMethod()
    {
        return $this->deploy_method;
    }

    public function getPluginSettings()
    {
        return $this->plugin_settings;
    }

    public function validate()
    {
        if (empty($this->cache_dir))
        {
            throw new Exception("Missing 'cache_dir' setting.");
        }

        if (empty($this->deploy_dir))
        {
            throw new Exception("Missing 'deploy_dir' setting.");
        }

        return $this;
    }
}
