<?php

namespace Dat0r\CodeGen\Parser\Config;

use Dat0r\CodeGen\Config;
use Dat0r\CodeGen\Parser\IParser;
use Dat0r\Common\Object;
use Dat0r\Common\Error\FilesystemException;
use Dat0r\Common\Error\ParseException;
use Dat0r\Common\Error\RuntimeException;

class ConfigIniParser extends Object implements IParser
{
    public function parse($ini_file, array $options = array())
    {
        $config_dir = dirname($ini_file);
        $settings = $this->loadIniFile($ini_file);

        return Config::create(
            array(
                'options' => array(
                    'deploy_method' => $this->determineDeployMethod($settings),
                    'deploy_dir' => $this->determineDeployDirectory($settings, $config_dir),
                    'cache_dir' => $this->determineCacheDirectory($settings, $config_dir),
                    'plugin_settings' => $this->createPluginData($settings, $config_dir)
                )
            )
        );
    }

    protected function loadIniFile($ini_file)
    {
        if (!is_readable(realpath($ini_file))) {
            throw new FilesystemException(
                sprintf('Unable to read config at path: `%s`', $ini_file)
            );
        }

        $settings = @parse_ini_file($ini_file, true);
        if ($settings === false) {
            throw new ParseException("Unable to parse given config file: $ini_file.");
        }

        return $settings;
    }

    protected function determineDeployMethod(array $settings)
    {
        $deploy_method = null;
        if (isset($settings['deploy_method'])) {
            $deploy_method = $settings['deploy_method'];
        } else {
            $deploy_method = 'copy';
        }

        return $deploy_method;
    }

    protected function determineDeployDirectory(array $settings, $config_dir)
    {
        if (!isset($settings['deploy_dir'])) {
            throw new RuntimeException(
                "Missing 'deploy_dir' setting within the provided config (.ini)file."
            );
        }

        $deploy_directory = null;
        if ($settings['deploy_dir']{0} === '.') {
            $deploy_directory = $this->resolveRelativePath(
                $settings['deploy_dir'],
                $config_dir
            );
        } else {
            $deploy_directory = $this->fixPath($settings['deploy_dir']);
        }

        return $deploy_directory;
    }

    protected function determineCacheDirectory(array $settings, $config_dir)
    {
        if (!isset($settings['cache_dir'])) {
            throw new RuntimeException(
                "Missing 'cache_dir' setting within the provided config (.ini)file."
            );
        }

        $cache_directory = null;
        if ($settings['cache_dir']{0} === '.') {
            $cache_directory = $this->resolveRelativePath(
                $settings['cache_dir'],
                $config_dir
            );
        } else {
            $cache_directory = $this->fixPath($settings['cache_dir']);
        }

        return $cache_directory;
    }

    protected function createPluginData(array $settings, $config_dir)
    {
        $plugin_settings = array();
        if (!isset($settings['plugins']) || !is_array($settings['plugins'])) {
            return $plugin_settings;
        }

        foreach ($settings['plugins'] as $plugin_class => $plugin_path) {
            if ($plugin_path{0} === '.') {
                $plugin_path = $this->resolveRelativePath($plugin_path, $config_dir);
            } else {
                $plugin_path = $this->fixPath($plugin_path);
            }

            $current_settings = array();
            if (isset($settings[$plugin_class]) && is_array($settings[$plugin_class])) {
                $current_settings = $settings[$plugin_class];
            }
            foreach ($current_settings as $key => &$value) {
                if (preg_match('~^(\./|\.\./)~is', $value)) {
                    $value = $this->resolveRelativePath($value, $config_dir);
                }
            }
            $plugin_settings[$plugin_class] = array(
                'implementor' => $plugin_class,
                'path' => $plugin_path,
                'options' => $current_settings
            );
        }

        return $plugin_settings;
    }

    protected function resolveRelativePath($path, $base)
    {
        $fullpath_parts = array();
        $path_parts = explode(DIRECTORY_SEPARATOR, $this->fixPath($base) . $path);
        foreach ($path_parts as $path_part) {
            if ($path_part === '..') {
                array_pop($fullpath_parts);
            } elseif ($path_part !== '.') {
                $fullpath_parts[] = $path_part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $fullpath_parts);
    }

    protected function fixPath($path)
    {
        $fixed_path = $path;
        if (empty($path)) {
            return $fixed_path;
        }
        if (DIRECTORY_SEPARATOR != $path{strlen($path) - 1}) {
            $fixed_path .= DIRECTORY_SEPARATOR;
        }

        return $fixed_path;
    }
}
