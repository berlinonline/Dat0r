<?php

namespace Dat0r\Tests\CodeGen;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Config;

class ConfigTest extends TestCase
{
    const FIX_CACHE_DIR = '/tmp/dat0r_test_cache';

    const FIX_DEPLOY_DIR = '/tmp/dat0r_test_deploy';

    public function testCreateConfig()
    {
        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => 'copy'
            )
        );

        $this->assertInstanceOf('Dat0r\CodeGen\Config', $config);
    }

    public function testConfigGetCacheDir()
    {
        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => 'copy'
            )
        );

        $this->assertEquals(self::FIX_CACHE_DIR, $config->getCacheDir());
    }

    public function testConfigGetDeployDir()
    {
        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => 'copy'
            )
        );

        $this->assertEquals(self::FIX_DEPLOY_DIR, $config->getDeployDir());
    }

    public function testConfigGetDefaultDeployMethod()
    {
        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR
            )
        );

        $this->assertEquals('copy', $config->getDeployMethod());
    }

    public function testConfigGetDeployMethod()
    {
        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => 'move'
            )
        );

        $this->assertEquals('move', $config->getDeployMethod());
    }

    public function testConfigGetPluginSettings()
    {
        $plugin_settings = array();

        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => 'copy',
                'plugin_settings' => $plugin_settings
            )
        );

        $this->assertEquals($plugin_settings, $config->getPluginSettings());
    }

    public function testValidateCorrectData()
    {
        $config = Config::create(
            array(
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR,
                'deploy_method' => 'copy'
            )
        );

        // test fluent api, as $this is returned on success
        $this->assertInstanceOf('Dat0r\CodeGen\Config', $config->validate());
    }

    /**
     * @expectedException Dat0r\Common\Error\InvalidConfigException
     */
    public function testValidateMissingCacheDir()
    {
        $config = Config::create(
            array('deploy_dir' => self::FIX_DEPLOY_DIR)
        );

        $config->validate();
        // @codeCoverageIgnoreStart
    }   // @codeCoverageIgnoreEnd


    /**
     * @expectedException Dat0r\Common\Error\InvalidConfigException
     */
    public function testValidateMissingDeployDir()
    {
        $config = Config::create(
            array('cache_dir' => self::FIX_CACHE_DIR)
        );

        $config->validate();
        // @codeCoverageIgnoreStart
    }   // @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\InvalidConfigException
     */
    public function testCreateWithInvalidDeployMethod()
    {
        Config::create(
            array(
                'deploy_method' => 'invalid_deploy_method',
                'cache_dir' => self::FIX_CACHE_DIR,
                'deploy_dir' => self::FIX_DEPLOY_DIR
            )
        );
        // @codeCoverageIgnoreStart
    }   // @codeCoverageIgnoreEnd
}
