<?php

namespace CMF\Core\CodeGenerator;

class AutoloadException extends \Exception {}

class Autoloader
{
    const PACKAGE_ROOT_NAMESPACE = 'CMF\Core\Runtime';

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
        $classPath = $baseName . '.class.php';
        $ifacePath = $baseName . '.iface.php';
        
        if (is_readable($classPath))
        {
            require_once $classPath;
        }
        else if (is_readable($ifacePath))
        {
            require_once $ifacePath;
        }
        else
        {
            throw new AutoloadException(
                "Unable to autoload demanded class: $class, tried locations $classPath and $ifacePath."
            );
        }
    }
}
