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

        if (isset($settings['cache_dir']) && $settings['cache_dir']{0} === '.') {
            $settings['cache_dir'] = $this->resolveRelativePath(
                $settings['cache_dir'],
                $config_dir
            );
        } elseif (isset($settings['cache_dir'])) {
            $settings['cache_dir'] = $this->fixPath($settings['cache_dir']);
        }

        if (isset($settings['deploy_dir']) && $settings['deploy_dir']{0} === '.') {
            $settings['deploy_dir'] = $this->resolveRelativePath(
                $settings['deploy_dir'],
                $config_dir
            );
        } elseif (isset($settings['deploy_dir'])) {
            $settings['deploy_dir'] = $this->fixPath($settings['deploy_dir']);
        }

        return $settings;
    }
}
