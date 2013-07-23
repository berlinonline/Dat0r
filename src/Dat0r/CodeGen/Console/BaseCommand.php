<?php

namespace Dat0r\CodeGen\Console;

use Symfony\Component\Console\Command;

abstract class BaseCommand extends Command\Command
{
    public function fixRelativePath($path_with_dots)
    {
        $array = explode(DIRECTORY_SEPARATOR, $path_with_dots);
        $domain = array_shift($array);

        $parents = array();
        foreach ($array as $dir) {
            switch ($dir) {
                case '.':
                // Don't need to do anything here
                break;
                case '..':
                    array_pop($parents);
                break;
                default:
                    $parents[] = $dir;
                break;
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

        if ('/' != $path{strlen($path) - 1}) {
            $fixed_path .= '/';
        }

        return $fixed_path;
    }
}
