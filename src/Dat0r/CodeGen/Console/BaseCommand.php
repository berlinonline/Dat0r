<?php

namespace Dat0r\CodeGen\Console;

use Symfony\Component\Console\Command;

abstract class BaseCommand extends Command\Command
{
    public function resolvePathRelativeToBaseDir($path, $base_dir)
    {
        if ($path{0} !== '.') {
            return $path;
        }

        $path_parts = explode(DIRECTORY_SEPARATOR, $this->fixPath($base_dir) . $path);

        $parents = array();
        foreach ($path_parts as $path_part) {
            if ($path_part === '..') {
                array_pop($parents);
            } elseif ($path_part !== '.')) {
                $parents[] = $path_part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $parents);
    }

    public function fixPath($path)
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
