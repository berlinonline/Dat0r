<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Common\Object;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\ValueHolder\NullValue;
use Dat0r\Runtime\Validator\IValidator;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Module\IModule;

/**
 * Base class that all Dat0r IField implementations should extend.
 * Provides a pretty complete implementation for the IField interface.
 *
 * basic options: 'validator', 'value_holder', 'default_value', 'mandatory'
 * @todo extends Object; which introduces a breaking change to the create method.
 */
abstract class Field implements IField
{
    /**
     * Holds a reference to the field's module.
     *
     * @var IModule $module;
     */
    protected $module;

    /**
     * Holds a reference to the parent field, if there is one.
     *
     * @var IField $parent;
     */
    protected $parent;

    /**
     * Holds the field's name.
     *
     * @var string $name
     */
    protected $name;

    /**
     * Holds the field's options.
     *
     * @var array $options
     */
    protected $options = array();

    /**
     * Holds the field's validator instance.
     *
     * @var IValidator $validator
     */
    protected $validator;

    /**
     * Constructs a new field instance.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        $this->name = $name;
        $this->options = $options;
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
     * Returns the field's module.
     *
     * @return IModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets the field's module once, if it isn't assigned.
     *
     * @param IModule $module
     */
    public function setModule(IModule $module)
    {
        if (!$this->module) {
            $this->module = $module;
        }
        // @todo else throw an exception,
        // as a second call to setModule might imply a logic error?
    }

    /**
     * Returns the field's parent, if it has one.
     *
     * @return IField
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the field's parent once, if it isn't yet assigned.
     *
     * @param IField $parent
     */
    public function setParent(IField $parent)
    {
        if (!$this->parent) {
            $this->parent = $parent;
        }
        // @todo else throw an exception,
        // as a second call to setParent might imply a logic error?
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
        return $this->getOption('default_value', $this->getNullValue());
    }

    /**
     * Returns a field's null value.
     *
     * @return mixed
     */
    public function getNullValue()
    {
        return $this->getOption('null_value', null);
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
            $default_validator_class = '\\Dat0r\\Runtime\\Validator\\Validator';
            $validator_implementor = $this->getOption('validator', $default_validator_class);

            if (!class_exists($validator_implementor, true)) {
                throw new RuntimeException(
                    sprintf(
                        "Unable to resolve validator implementor '%s' given for field: '%s'.",
                        $validator_implementor,
                        $this->getName()
                    )
                );
            }

            $validator = new $validator_implementor($this->getName(), $this->buildValidationRules());
            if (!$validator instanceof IValidator) {
                throw new InvalidTypeException(
                    sprintf(
                        "Invalid validator implementor '%s' given for field: '%s'." .
                        "Make sure to implement 'Dat0r\Runtime\Validator\Validator\IValidator'.",
                        $validator_implementor,
                        $this->getName()
                    )
                );
            }
            $this->validator = $validator;
        }

        return $this->validator;
    }

    /**
     * Creates a IValueHolder, that is specific to the current field instance.
     *
     * @return IValueHolder
     */
    public function createValueHolder()
    {
        $implementor = $this->hasOption('value_holder')
            ? $this->getOption('value_holder')
            : $this->buildDefaultValueHolderClassName();

        if (!class_exists($implementor)) {
            throw new RuntimeException(
                "Invalid field value-holder given upon createValueHolder request."
            );
        }
        $value_holder = $implementor::create($this);

        if (!$value_holder instanceof IValueHolder) {
            throw new InvalidTypeException(
                sprintf(
                    "Invalid valueholder implementation '%s' given for field '%s'.",
                    $implementor,
                    $this->getField()->getName()
                )
            );
        }

        $value_holder->setValue($this->getDefaultValue());

        return $value_holder;
    }

    /**
     * Build a list of rules used by the field's validator to validate values for this field.
     *
     * @return RuleList
     */
    protected function buildValidationRules()
    {
        return new RuleList();
    }

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function buildDefaultValueHolderClassName()
    {
        $valueholder_namespace = "\\Dat0r\\Runtime\\ValueHolder\\Type";
        $field_classname_parts = explode('\\', get_class($this));
        $valueholder_class = preg_replace('~(.*)Field$~is', '$1ValueHolder', array_pop($field_classname_parts));

        return $valueholder_namespace . '\\' . $valueholder_class;
    }
}
