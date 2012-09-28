<?php

namespace CMF\Core\CodeGenerator;

class CliOptionsProvider
{
    const PARSE_EXPRESSION = 'c:d:a:';

    public static function getOptions()
    {
        $optsOut = array();
        $optsIn = getopt(self::PARSE_EXPRESSION);
        if (2 > count($optsIn))
        {
            throw new \OptionParseException("Invalid number of arguments given.");
        }
        if (! isset($optsIn['d']) || ! isset($optsIn['c']))
        {
            throw new \OptionParseException("Missing one of either options: config,module_defintiion,action.");
        }

        $filePath = realpath($optsIn['d']);
        if (! $filePath || ! is_readable($filePath))
        {
            throw new \OptionParseException(
                "The given module definition is not readable at path: " . $optsIn['d']
            );
        }
        $configPath = realpath($optsIn['c']);
        if (! $configPath || ! is_readable($configPath))
        {
            throw new \OptionParseException(
                "The given config file is not readable at path: " . $optsIn['c']
            );
        }

        $validActions = array(CliInterface::ACTION_GENERATE, CliInterface::ACTION_DEPLOY);
        $actions = array();
        foreach (explode('+', $optsIn['a']) as $action)
        {
            if (in_array($action, $validActions))
            {
                $actions[] = $action;
            }
        }
        if (empty($actions))
        {
            throw new OptionParseException(
                sprintf("Invalid action %s given.", $optsIn['a'])
            );
        }
        return array(
            'config' => $configPath, 
            'module_definition' => $filePath,
            'actions' => $actions
        );
    }
}

class OptionParseException extends \Exception{}
