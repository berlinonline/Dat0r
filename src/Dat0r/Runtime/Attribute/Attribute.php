<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Common\Object;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\ValueHolder\NullValue;
use Dat0r\Runtime\Validator\IValidator;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Module\IModule;

/**
 * Base class that all Dat0r IAttribute implementations should extend.
 * Provides a pretty complete implementation for the IAttribute interface.
 *
 * basic options: 'validator', 'value_holder', 'default_value', 'mandatory'
 * @todo extends Object; which introduces a breaking change to the create method.
 */
abstract class Attribute implements IAttribute
{
    /**
     * Holds a reference to the attribute's module.
     *
     * @var IModule $module;
     */
    protected $module;

    /**
     * Holds a reference to the parent attribute, if there is one.
     *
     * @var IAttribute $parent;
     */
    protected $parent;

    /**
     * Holds the attribute's name.
     *
     * @var string $name
     */
    protected $name;

    /**
     * Holds the attribute's options.
     *
     * @var array $options
     */
    protected $options = array();

    /**
     * Holds the attribute's validator instance.
     *
     * @var IValidator $validator
     */
    protected $validator;

    /**
     * Constructs a new attribute instance.
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
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the attribute's module.
     *
     * @return IModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets the attribute's module once, if it isn't assigned.
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
     * Returns the attribute's parent, if it has one.
     *
     * @return IAttribute
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the attribute's parent once, if it isn't yet assigned.
     *
     * @param IAttribute $parent
     */
    public function setParent(IAttribute $parent)
    {
        if (!$this->parent) {
            $this->parent = $parent;
        }
        // @todo else throw an exception,
        // as a second call to setParent might imply a logic error?
    }

    /**
     * Returns the attribute's options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns a attribute option by name if it exists.
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
     * Tells if a attribute currently owns a specific option.
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
     * Returns the default value of the attribute.
     *
     * @return IValueHolder
     */
    public function getDefaultValue()
    {
        return $this->getOption('default_value', $this->getNullValue());
    }

    /**
     * Returns a attribute's null value.
     *
     * @return mixed
     */
    public function getNullValue()
    {
        return $this->getOption('null_value', null);
    }

    /**
     * Returns the IValidator implementation to use when validating values for this attribute.
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
                        "Unable to resolve validator implementor '%s' given for attribute: '%s'.",
                        $validator_implementor,
                        $this->getName()
                    )
                );
            }

            $validator = new $validator_implementor($this->getName(), $this->buildValidationRules());
            if (!$validator instanceof IValidator) {
                throw new InvalidTypeException(
                    sprintf(
                        "Invalid validator implementor '%s' given for attribute: '%s'." .
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
     * Creates a IValueHolder, that is specific to the current attribute instance.
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
                "Invalid attribute value-holder given upon createValueHolder request."
            );
        }
        $value_holder = $implementor::create($this);

        if (!$value_holder instanceof IValueHolder) {
            throw new InvalidTypeException(
                sprintf(
                    "Invalid valueholder implementation '%s' given for attribute '%s'.",
                    $implementor,
                    $this->getAttribute()->getName()
                )
            );
        }

        $value_holder->setValue($this->getDefaultValue());

        return $value_holder;
    }

    /**
     * Build a list of rules used by the attribute's validator to validate values for this attribute.
     *
     * @return RuleList
     */
    protected function buildValidationRules()
    {
        return new RuleList();
    }

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this attribute.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function buildDefaultValueHolderClassName()
    {
        $valueholder_namespace = "\\Dat0r\\Runtime\\ValueHolder\\Type";
        $attribute_classname_parts = explode('\\', get_class($this));
        $valueholder_class = array_pop($attribute_classname_parts) . 'ValueHolder';

        return $valueholder_namespace . '\\' . $valueholder_class;
    }
}
