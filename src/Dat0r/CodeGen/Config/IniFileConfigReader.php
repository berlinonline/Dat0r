<?php

namespace Dat0r\CodeGen\Config;

use Dat0r;

class IniFileConfigReader extends ConfigReader
{
    public function read($file_path)
    {
        if (!is_readable(realpath($file_path))) {
            throw new Exception(
                sprintf('Unable to read config at path: `%s`', $file_path)
            );
        }

        $config_dir = dirname($file_path);
        $settings = @parse_ini_file($file_path, true);
        if ($settings === false) {
            throw new Exception("Unable to parse given config file: $file_path.");
        }

        $parsed_settings = array();

        if (isset($settings['deploy_method'])) {
            $parsed_settings['deploy_method'] = $settings['deploy_method'];
        } else {
            $parsed_settings['deploy_method'] = 'copy';
        }

        if (isset($settings['cache_dir']) && $settings['cache_dir']{0} === '.') {
            $parsed_settings['cache_dir'] = $this->resolveRelativePath(
                $settings['cache_dir'],
                $config_dir
            );
        } elseif (isset($settings['cache_dir'])) {
            $parsed_settings['cache_dir'] = $this->fixPath($settings['cache_dir']);
        }

        if (isset($settings['deploy_dir']) && $settings['deploy_dir']{0} === '.') {
            $parsed_settings['deploy_dir'] = $this->resolveRelativePath(
                $settings['deploy_dir'],
                $config_dir
            );
        } elseif (isset($settings['deploy_dir'])) {
            $parsed_settings['deploy_dir'] = $this->fixPath($settings['deploy_dir']);
        }

        $plugin_settings = array();
        if (isset($settings['plugins']) && is_array($settings['plugins'])) {
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
        }
        $parsed_settings['plugin_settings'] = $plugin_settings;

        return $parsed_settings;
    }
}
