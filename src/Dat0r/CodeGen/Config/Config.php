<?php

namespace Dat0r\CodeGen\Config;

use Dat0r\Type\Object;

class Config extends Object implements IConfig
{
    protected $cache_dir;

    protected $deploy_dir;

    protected $deploy_method = self::DEPLOY_COPY;

    protected $plugin_settings = array();

    public function getCacheDir()
    {
        return $this->cache_dir;
    }

    public function setCacheDir($cache_dir)
    {
        $this->cache_dir = trim($cache_dir);
    }

    public function getDeployDir()
    {
        return $this->deploy_dir;
    }

    public function setDeployDir($deploy_dir)
    {
        $this->deploy_dir = trim($deploy_dir);
    }

    public function getDeployMethod()
    {
        return $this->deploy_method;
    }

    public function setDeployMethod($deploy_method)
    {
        $valid_methods = array(self::DEPLOY_COPY, self::DEPLOY_MOVE);

        if (!in_array($deploy_method, $valid_methods)) {
            throw new Exception(
                sprintf("Invalid deploy method '%s' passed to config.", $deploy_method)
            );
        }

        $this->deploy_method = $deploy_method;
    }

    public function getPluginSettings()
    {
        return $this->plugin_settings;
    }

    public function validate()
    {
        if (empty($this->cache_dir)) {
            throw new Exception("Missing 'cache_dir' setting.");
        }

        if (empty($this->deploy_dir)) {
            throw new Exception("Missing 'deploy_dir' setting.");
        }

        return $this;
    }
}
