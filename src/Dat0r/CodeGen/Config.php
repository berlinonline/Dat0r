<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Configurable;
use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Common\Parameters;

class Config extends Configurable
{
    public function getBootstrapFile()
    {
        return $this->getParameter('bootstrap_file');
    }

    public function getCacheDir()
    {
        return $this->getParameter('cache_dir');
    }

    public function setCacheDir($cache_dir)
    {
        $this->setParameter('cache_dir', $cache_dir);
    }

    public function getDeployDir()
    {
        return $this->getParameter('deploy_dir');
    }

    public function setDeployDir($deploy_dir)
    {
        $this->setParameter('deploy_dir', $deploy_dir);
    }

    public function getDeployMethod()
    {
        return $this->getParameter('deploy_method', 'copy');
    }

    public function setDeployMethod($deploy_method)
    {
        $this->setParameter('deploy_method', $deploy_method);
    }

    public function getPluginSettings()
    {
        return $this->getParameter('plugin_settings', new Parameters());
    }

    public function validate()
    {
        $cache_directory = $this->getCacheDir();
        if (empty($cache_directory)) {
            throw new InvalidConfigException("Missing 'cache_dir' setting.");
        }

        $deploy_directory = $this->getDeployDir();
        if (empty($deploy_directory)) {
            throw new InvalidConfigException("Missing 'deploy_dir' setting.");
        }

        $deploy_method = $this->getDeployMethod();
        $valid_methods = array('copy', 'move');
        if (!in_array($deploy_method, $valid_methods)) {
            throw new InvalidConfigException(
                sprintf("Invalid deploy method '%s' passed to config.", $deploy_method)
            );
        }

        return $this;
    }
}
