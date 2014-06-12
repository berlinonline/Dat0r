<?php

namespace Dat0r\Runtime\Type;

use Dat0r\Runtime\Attribute\Bundle\Text;

/**
 * Provides behaviour in the context of being a top-level (aggregate root) type.
 */
abstract class AggregateRoot extends Type
{
    /**
     * Holds a list of IType implementations that are pooled by type.
     *
     * @var array $instances
     */
    protected static $instances = array();

    /**
     * Returns the pooled instance of a specific type.
     * Each type is pooled exactly once, making this a singleton style (factory)method.
     * This method is used to provide a convenient access to generated domain type instances.
     *
     * @return IType
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            $type = new static();
            self::$instances[$class] = $type;
        }

        return self::$instances[$class];
    }
}
