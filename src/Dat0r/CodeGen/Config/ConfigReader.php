<?php

namespace Dat0r\CodeGen\Config;

use Dat0r;

abstract class ConfigReader extends Dat0r\Object implements IConfigReader
{
    protected function resolveRelativePath($path, $base)
    {
        $path = explode('/', ltrim($path, '/'));
        $base = explode('/', rtrim($base, '/'));

        for(;;) {
            if (reset($path) === '..') {
                array_shift($path);
                array_pop($base);
            } else {
                break;
            }

            if (count($base) <= 1) {
                throw Exception('Exceeded base path!');
            }
        }

        return implode($base, '/')."/".implode($path, '/');
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
