<?php

namespace Dat0r\Tests\CodeGen\Config;

use Dat0r\Tests;
use Dat0r\CodeGen\Config;

class ConfigTest extends Tests\TestCase
{
    const FIX_CACHE_DIR = '/tmp/dat0r_test_cache';

    const FIX_DEPLOY_DIR = '/tmp/dat0r_test_deploy';

    public function testCreateConfig()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => Config\IConfig::DEPLOY_COPY
            )
        );

        $this->assertInstanceOf('Dat0r\CodeGen\Config\IConfig', $config);
    }

    public function testConfigGetCacheDir()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => Config\IConfig::DEPLOY_COPY
            )
        );

        $this->assertEquals(self::FIX_CACHE_DIR, $config->getCacheDir());
    }

    public function testConfigGetDeployDir()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => Config\IConfig::DEPLOY_COPY
            )
        );

        $this->assertEquals(self::FIX_DEPLOY_DIR, $config->getDeployDir());
    }

    public function testConfigGetDefaultDeployMethod()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR
            )
        );

        $this->assertEquals(Config\IConfig::DEPLOY_COPY, $config->getDeployMethod());
    }

    public function testConfigGetDeployMethod()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => Config\IConfig::DEPLOY_MOVE
            )
        );

        $this->assertEquals(Config\IConfig::DEPLOY_MOVE, $config->getDeployMethod());
    }

    public function testConfigGetPluginSettings()
    {
        $plugin_settings = array();

        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => Config\IConfig::DEPLOY_COPY,
                'plugin_settings' => $plugin_settings
            )
        );

        $this->assertEquals($plugin_settings, $config->getPluginSettings());
    }

    public function testValidateCorrectData()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => Config\IConfig::DEPLOY_COPY
            )
        );

        // test fluent api, as $this is returned on success
        $this->assertInstanceOf('Dat0r\CodeGen\Config\IConfig', $config->validate());
    }

    /**
     * @expectedException Dat0r\CodeGen\Config\Exception
     */
    public function testValidateMissingCacheDir()
    {
        $config = Config\Config::create(
            array('deploy_dir' => self::FIX_DEPLOY_DIR)
        );

        $config->validate();
    }

    /**
     * @expectedException Dat0r\CodeGen\Config\Exception
     */
    public function testValidateMissingDeployDir()
    {
        $config = Config\Config::create(
            array('cache_dir' => self::FIX_CACHE_DIR)
        );

        $config->validate();
    }

    /**
     * @expectedException Dat0r\CodeGen\Config\Exception
     */
    public function testCreateWithInvalidDeployMethod()
    {
        Config\Config::create(
            array(
                'deploy_method' => 'invalid_deploy_method',
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR
            )
        );
    }
}
