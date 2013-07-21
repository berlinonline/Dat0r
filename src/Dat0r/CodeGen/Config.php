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
}
