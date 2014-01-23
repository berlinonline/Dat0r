<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Configurable;
use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Common\Error\FilesystemException;

class Config extends Configurable
{
    protected $cache_dir;

    protected $deploy_dir;

    protected $deploy_method = 'copy';

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
        $valid_methods = array('copy', 'move');

        if (!in_array($deploy_method, $valid_methods)) {
            throw new InvalidConfigException(
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
            throw new InvalidConfigException("Missing 'cache_dir' setting.");
        }
        if (empty($this->deploy_dir)) {
            throw new InvalidConfigException("Missing 'deploy_dir' setting.");
        }

        return $this;
    }
}
