<?php

namespace Dat0r\CodeGen\Config;

use Dat0r;

abstract class ConfigReader extends Dat0r\Object implements IConfigReader
{
    protected function resolveRelativePath($path, $base)
    {
        $path_parts = explode(DIRECTORY_SEPARATOR, $this->fixPath($base) . $path);
        $parents = array();

        foreach ($path_parts as $path_part) {
            if ($path_part === '..') {
                array_pop($parents);
            } elseif ($path_part !== '.') {
                $parents[] = $path_part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $parents);
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
