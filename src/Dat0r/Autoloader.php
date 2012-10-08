<?php

namespace Dat0r;

class AutoloadException extends \Exception {}

class Autoloader
{
    /**
     * Holds a list of domain namespaces mapped to corresponding base dirs for autoloading.
     *
     * @var array $domainPackages
     */
    private static $domainPackages;

    /**
     * Maps Dat0r package namespaces to their corresponding base dir to use for autloading.
     *
     * @var array $corePackages
     */
    private static $corePackages;

    /**
     * Registers the autoloader to be used by the current process.
     * You may provide the base locations of any generated modules
     * in order to have them autoloaded accordingly.
     *
     * @param array $domainPackages
     */
    static public function register(array $domainPackages = array())
    {
        if (! self::$corePackages)
        {
            $here = __DIR__;
            self::$corePackages = array(
                'Dat0r\\Core' => $here . DIRECTORY_SEPARATOR . 'Core',
                'Dat0r\\Composer' => $here . DIRECTORY_SEPARATOR . 'Composer',
                'Dat0r\\Tests' => dirname(dirname($here)) . '/test/src/Dat0r'
            );

            self::$domainPackages = array();

            ini_set('unserialize_callback_func', 'spl_autoload_call');
            spl_autoload_register(array(new self, 'autoload'));
        }
        
        self::$domainPackages = array_merge(self::$domainPackages, $domainPackages);
    }

    /**
     * Autoloads a given class.
     *
     * @param string $class
     */
    static public function autoload($class)
    {
        if (($filePath = (0 === strpos($class, 'Dat0r')) ? self::buildCorePath($class) : self::buildDomainPath($class)))
        {
            self::tryRequire($filePath);
        }
    }

    /**
     * Try to build a path for a Dat0r class.
     *
     * @param string $class
     *
     * @return string
     */
    private static function buildCorePath($class)
    {
        $filePath = NULL;
        foreach (self::$corePackages as $rootNs => $baseDir)
        {
            if (0 === strpos($class, $rootNs))
            {
                $filePath = self::buildPath($class, $rootNs, $baseDir);
                break;
            }
        }
        return $filePath;
    }

    /**
     * Try to build a path for a given domain class.
     *
     * @param string $class
     *
     * @return string
     */
    private static function buildDomainPath($class)
    {
        $filePath = NULL;
        foreach (self::$domainPackages as $rootNs => $baseDir)
        {
            if (0 === strpos($class, $rootNs))
            {
                $filePath = self::buildPath($class, $rootNs, $baseDir);
                break;
            }
        }
        return $filePath;
    }

    /**
     * Builds the class filepath for a given class.
     *
     * @param string $class
     *
     * @return string
     */
    private static function buildPath($class, $rootNs, $baseDir)
    {
        $baseName = str_replace(
            array($rootNs, '\\'),
            array('', DIRECTORY_SEPARATOR),
            $class
        );
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
