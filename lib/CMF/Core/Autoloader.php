<?php

namespace CMF\Core;

class AutoloadException extends \Exception {}

class Autoloader
{
    private static $rootNamespace;

    static public function register()
    {
        $class = new \ReflectionClass(__CLASS__);
        self::$rootNamespace = $class->getNamespaceName();

        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    static public function autoload($class)
    {
        if (0 !== strpos($class, self::$rootNamespace)) 
        {
            return;
        }

        $baseName = dirname(__FILE__) . str_replace(
            array(self::$rootNamespace, '\\'), 
            array('', DIRECTORY_SEPARATOR), 
            $class
        );
        if (is_readable($classPath = $baseName . '.php'))
        {
            require_once $classPath;
        }
        else
        {
            throw new AutoloadException(
                "Unable to autoload demanded class: $class at location: $baseName.php."
            );
        }
    }
}
