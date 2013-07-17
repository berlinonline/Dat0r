<?php

namespace Dat0r\Core\Field;

use Dat0r\Core\Error;
use Dat0r\Core\Freezable;
use Dat0r\Core\ValueHolder\IValueHolder;
use Dat0r\Core\ValueHolder\NullValue;

/**
 * Base class that all Dat0r IField implementations should extend.
 * Provides a pretty complete implementation for the IField interface.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class Field extends Freezable implements IField
{
    /**
     * Holds the option name of the option that provides the IValueHolder implementor to use
     * for managing field specific data.
     */
    const OPT_VALUE_HOLDER = 'value_holder';

    /**
     * Holds the option name of the option that provides the IValidator implementor to use
     * when validating field specific data.
     */
    const OPT_VALIDATOR = 'validator';

    const OPT_VALUE_CONSTRAINT = 'constraints';

    const OPT_VALUE_DEFAULT = 'default_value';

    /**
     * Holds the field's name.
     *
     * @var string $name
     */
    protected $name;

    /**
     * Holds the field'S options.
     *
     * @var array $options
     */
    protected $options = array();

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function getValueHolderImplementor()
    {
        return preg_replace_callback('/(.*)\\Field(.*)Field$/is', function($matches)
        {
            return sprintf('%sValueHolder%sValueHolder', $matches[1], $matches[2]);
        }, get_class($this));
    }

    /**
     * Returns the IValidator implementation to use when validating values for this field.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValidator implementation.
     */
    protected function getValidationImplementor()
    {
        return preg_replace_callback('/(.*)\\Field(.*)Field$/is', function($matches)
        {
            return sprintf('%sValidator%sValidator', $matches[1], $matches[2]);
        }, get_class($this));
    }

    /**
     * Creates a new field instance.
     *
     * @param string $name
     * @param array $options
     *
     * @return IField
     */
    public static function create($name, array $options = array())
    {
        return new static($name, $options);
    }

    /**
     * Returns the name of the field.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Validates a given value with a strategy dedicated to the field.
     *
     * @param mixed $value
     * 
     * @return boolean
     */
    public function validate($value)
    {
        $implementor = $this->hasOption(self::OPT_VALIDATOR) 
            ? $this->getOption(self::OPT_VALIDATOR)
            : $this->getValidationImplementor();
        if (! class_exists($implementor))
        {
            throw new Error\InvalidImplementorException(
                "Invalid field validator given upon validate request."
            );
        }
        $validator = $implementor::create($this);
        // @todo check against instanceof IValidator
        return $validator->validate($value);
    }

    /**
     * Returns the default value of the field.
     *
     * @return IValueHolder
     */
    public function getDefaultValue()
    {
        if ($this->hasOption(self::OPT_VALUE_DEFAULT))
        {
            return $this->getOption(self::OPT_VALUE_DEFAULT);
        }
        
        return NULL;
    }

    /**
     * Returns a field option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = NULL)
    {
        return $this->hasOption($name) ? $this->options[$name] : $default;
    }

    /**
     * Tells if a field currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Creates a IValueHolder instance dedicated to the current field instance.
     *
     * @param mixed $value
     *
     * @return IValueHolder
     */
    public function createValueHolder($value)
    {
        $implementor = $this->hasOption(self::OPT_VALUE_HOLDER) 
            ? $this->getOption(self::OPT_VALUE_HOLDER)
            : $this->getValueHolderImplementor();

        if (! class_exists($implementor))
        {
            throw new Error\InvalidImplementorException(
                "Invalid field value-holder given upon createValueHolder request."
            );
        }
        $valueHolder = $implementor::create($this, $value);
        // @todo check against instanceof IValueHolder?
        return $valueHolder;
    }

    public function getValueTypeConstraint()
    {
        $constraints = $this->getOption(self::OPT_VALUE_CONSTRAINT);
        $valueType = 'dynamic';

        if (isset($constraints['value_type']))
        {
            $valueType = $constraints['value_type'];
        }

        return $valueType; 
    }

    /**
     * Constructs a new field instance.
     *
     * @param string $name
     * @param array $options
     */
    protected function __construct($name, array $options = array())
    {
        $this->name = $name;
        $this->options = $options;
    }
}
