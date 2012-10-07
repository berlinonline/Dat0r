<?php

namespace Dat0r\Core\Runtime\Field;

use Dat0r\Core\Runtime;
use Dat0r\Runtime\Error;
use Dat0r\Core\Runtime\ValueHolder;

/**
 * Base class that all Dat0r fields should extend.
 */
abstract class Field extends Runtime\Freezable implements IField
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

    /**
     * @var string $name Holds the field's name.
     */
    protected $name;

    /**
     * @var array $options Holds the field'S options.
     */
    protected $options = array();

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    abstract protected function getValueHolderImplementor();

    /**
     * Returns the IValidator implementation to use when validating values for this field.
     *
     * @return string Fully qualified name of an IValidator implementation.
     */
    abstract protected function getValidationImplementor();

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
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
     */
    public function getDefaultValue()
    {
        return ValueHolder\NullValue::create($this);
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
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
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
        // @todo check against instanceof IValueHolder
        return $valueHolder;
    }

    /**
     * Constructs a new Dat0r\Core\Runtime\Module\Module.
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
