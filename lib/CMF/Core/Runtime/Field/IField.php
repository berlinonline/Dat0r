<?php

namespace CMF\Core\Runtime\Field;

use CMF\Core\Runtime;
use CMF\Core\Runtime\ValueHolder;

/**
 * @todo write a meaningfull text that explains what fields are and what they do.
 */
interface IField extends Runtime\IFreezable
{
    /**
     * Creates a new field instance.
     *
     * @return IField
     */
    public static function create($name, array $options = array());

    /**
     * Returns the name of the field.
     * 
     * @return string
     */
    public function getName();

    /**
     * Validates a given value with a strategy dedicated to the field.
     * 
     * @return boolean
     */
    public function validate($value);

    /**
     * Returns the default value of the field.
     *
     * @return CMF\Core\Runtime\ValueHolder\IValueHolder
     */
    public function getDefaultValue();

    /**
     * Returns a field option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = NULL);

    /**
     * Tells if a field currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name);

    /**
     * Creates a IValueHolder instance dedicated to the current field instance.
     *
     * @return CMF\Core\Runtime\ValueHolder\IValueHolder
     */
    public function createValueHolder($value);
}
