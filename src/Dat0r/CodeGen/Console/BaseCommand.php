<?php

namespace Dat0r\CodeGen\Console;

use Symfony\Component\Console\Command;

abstract class BaseCommand extends Command\Command
{
    public function fixRelativePath($path_with_dots)
    {
        $fixed_path = $path_with_dots;

        do {
            $fixed_path = preg_replace('#[^/\.]+/\.\./#', '', $fixed_path, -1, $count);
        } while ($count);

        $fixed_path = str_replace(array('/./', '//'), '/', $fixed_path);

        return $fixed_path;
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
