<?php

namespace Dat0r;

/**
 * Provides autoloading for all classes inside the Dat0r namespace
 * along with support for registering additional namespaces for psr-0 autoloading
 * relative to corresponding base paths.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class Autoloader
{
    /**
     * Holds a list of domain namespaces mapped to corresponding base dirs for autoloading.
     *
     * @var array $domain_packages
     */
    static private $domain_packages;

    /**
     * Maps Dat0r package namespaces to their corresponding base dir to use for autloading.
     *
     * @var array $core_packages
     */
    static private $core_packages;

    /**
     * Registers the autoloader to be used by the current process.
     * You may provide the base locations of any generated modules
     * in order to have them autoloaded accordingly.
     *
     * @param array $domain_packages
     *
     * @codeCoverageIgnoreStart
     */
    public static function register(array $domain_packages = array())
    {
        if (! self::$core_packages) {
            $here = __DIR__;
            self::$core_packages = array(
                'Dat0r\\Core' => $here . DIRECTORY_SEPARATOR . 'Core',
                'Dat0r\\Tests' => dirname(dirname($here)) . '/tests/src/Dat0r'
            );

            self::$domain_packages = array();

            ini_set('unserialize_callback_func', 'spl_autoload_call');
            spl_autoload_register(array(new self, 'autoload'));
        }

        self::$domain_packages = array_merge(self::$domain_packages, $domain_packages);
    }
    // @codeCoverageIgnoreEnd

    /**
     * Autoloads a given class.
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        $file_path = null;

        if (0 === strpos($class, 'Dat0r')) {
            $file_path = self::buildCorePath($class);
        } else {
            $file_path = self::buildDomainPath($class);
        }

        if ($file_path) {
            self::tryRequire($file_path);
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
        $file_path = null;
        foreach (self::$core_packages as $namespace => $base_dir) {
            if (0 === strpos($class, $namespace)) {
                $file_path = self::buildPath($class, $namespace, $base_dir);
                break;
            }
        }
        return $file_path;
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
        $file_path = null;

        foreach (self::$domain_packages as $namespace => $base_dir) {
            if (0 === strpos($class, $namespace)) {
                $file_path = self::buildPath($class, $namespace, $base_dir);
                break;
            }
        }

        return $file_path;
    }

    /**
     * Builds the class filepath for a given class.
     *
     * @param string $class
     *
     * @return string
     */
    private static function buildPath($class, $namespace, $base_dir)
    {
        $base_name = str_replace(
            array($namespace, '\\'),
            array('', DIRECTORY_SEPARATOR),
            $class
        );

        return $base_dir . DIRECTORY_SEPARATOR . $base_name . '.php';
    }

    /**
     * Tries to require a given file.
     *
     * @param string $file_path
     *
     * @return mixed
     */
    private static function tryRequire($file_path)
    {
        if (! is_readable($file_path)) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'AutoloadException.php';

            throw new AutoloadException(
                "Unable to autoload demanded class at location: $file_path."
            );
        }

        require $file_path;
    }
}
