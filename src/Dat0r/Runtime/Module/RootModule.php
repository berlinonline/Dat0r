<?php

namespace Dat0r\Runtime\Module;

use Dat0r\Runtime\Field\Type\TextField;

/**
 * Provides behaviour in the context of being a top-level (aggregate root) module.
 */
abstract class RootModule extends Module
{
    /**
     * Holds a list of IModule implementations that are pooled by type.
     *
     * @var array $instances
     */
    protected static $instances = array();

    /**
     * Returns the pooled instance of a specific module.
     * Each module is pooled exactly once, making this a singleton style (factory)method.
     * This method is used to provide a convenient access to generated domain module instances.
     *
     * @return IModule
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            $module = new static();
            self::$instances[$class] = $module;
        }

        return self::$instances[$class];
    }
}
