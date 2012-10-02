<?php

namespace Dat0r;

class AutoloadException extends \Exception {}

class Autoloader
{
    const NS_ROOT = 'Dat0r';

    /**
     * Holds a list of generated domain packages,
     * mapping their namespaces to correspondig base directories.
     *
     * @var array $domainPackages
     */
    private static $domainPackages;

    /**
     * Registers the autoloader to be used by the current process.
     * You may provide the base locations of any generated modules
     * in order to have them autoloaded accordingly.
     *
     * @param array $domainPackages
     */
    static public function register(array $domainPackages = array())
    {
        self::$domainPackages = $domainPackages;

        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Autoloads a given class.
     *
     * @param string $class
     */
    static public function autoload($class)
    {
        $filePath = FALSE;

        if (0 === strpos($class, self::NS_ROOT . '\Core'))
        {
            $filePath = self::buildCorePath($class);
        }
        else
        {
            foreach (self::$domainPackages as $rootNs => $baseDir)
            {
                if (0 === strpos($class, $rootNs))
                {
                    $filePath = self::buildDomainPath($class, $rootNs, $baseDir);
                    break;
                }
            }
        }
        
        if ($filePath)
        {
            self::tryRequire($filePath);
        }
    }

    /**
     * Builds the class filepath for a given core class.
     *
     * @param string $class
     *
     * @return string
     */
    private static function buildCorePath($class)
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace(
            array(self::NS_ROOT, '\\'),
            array('', DIRECTORY_SEPARATOR),
            $class
        ) . '.php';
    }

    /**
     * Builds the class filepath for a given domain class.
     *
     * @param string $class
     *
     * @return string
     */
    private static function buildDomainPath($class, $rootNs, $baseDir)
    {
        $baseName = str_replace(
            array($rootNs, '\\'),
            array('', DIRECTORY_SEPARATOR),
            $class
        );
        if (0 === strpos($baseName, '/Base'))
        {
            $baseDir .= DIRECTORY_SEPARATOR . 'base';
        }
        return $baseDir . DIRECTORY_SEPARATOR . $baseName . '.php';
    }

    /**
    * Tries to require a given file.
    *
    * @param string $filePath
    *
    * @return mixed
    */
    private static function tryRequire($filePath)
    {
        if (! is_readable($filePath))
        {
            throw new AutoloadException(
                "Unable to autoload demanded class at location: $filePath."
            );
        }

        require $filePath;
    }
}
