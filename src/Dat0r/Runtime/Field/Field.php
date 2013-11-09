<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\ValueHolder\NullValue;
use Dat0r\Runtime\Validation\Validator\IValidator;
use Dat0r\Runtime\Validation\Rule\RuleList;

/**
 * Base class that all Dat0r IField implementations should extend.
 * Provides a pretty complete implementation for the IField interface.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class Field implements IField
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

    protected $validator;

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
     * Returns the field's options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
    public function getOption($name, $default = null)
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
     * Returns the default value of the field.
     *
     * @return IValueHolder
     */
    public function getDefaultValue()
    {
        return $this->getOption(self::OPT_VALUE_DEFAULT);
    }

    /**
     * Returns the IValidator implementation to use when validating values for this field.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValidator implementation.
     */
    public function getValidator()
    {
        if (!$this->validator) {
            $default_implementor = '\\Dat0r\\Runtime\\Validation\\Validator\\Validator';
            $implementor = $this->getOption('validator', $default_implementor);

            if (!class_exists($implementor, true)) {
                throw new RuntimeException(
                    sprintf(
                        "Unable to resolve validator implementor '%s' given for field: '%s'.",
                        $implementor,
                        $this->getName()
                    )
                );
            }

            $validator = new $implementor($this->getName(), $this->getValidationRules());
            if (!$validator instanceof IValidator) {
                throw new RuntimeException(
                    sprintf(
                        "Invalid validator implementor '%s' given for field: '%s'." .
                        "Make sure to implement 'Dat0r\Runtime\Validation\Validator\IValidator'.",
                        $implementor,
                        $this->getName()
                    )
                );
            }
            $this->validator = $validator;
        }

        return $this->validator;
    }

    public function getValidationRules()
    {
        return new RuleList();
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

        if (!class_exists($implementor)) {
            throw new RuntimeException(
                "Invalid field value-holder given upon createValueHolder request."
            );
        }
        $value_holder = $implementor::create($this, $value);
        // @todo check against instanceof IValueHolder?
        return $value_holder;
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

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function getValueHolderImplementor()
    {
        return preg_replace_callback(
            '/(.*)\\Field(.*)Field$/is',
            function ($matches) {
                $impl_pattern = '%sValueHolder%sValueHolder';
                return sprintf($impl_pattern, $matches[1], $matches[2]);
            },
            get_class($this)
        );
    }
}
