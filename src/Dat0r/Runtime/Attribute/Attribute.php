<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Common\Object;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Runtime\Attribute\Value\IValue;
use Dat0r\Runtime\Attribute\Value\NullValue;
use Dat0r\Runtime\Validator\IValidator;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\IDocumentType;

/**
 * Base class that all Dat0r IAttribute implementations should extend.
 * Provides a pretty complete implementation for the IAttribute interface.
 *
 * basic options: 'validator', 'value', 'default_value', 'null_value', 'mandatory'
 * @todo extends Object; which introduces a breaking change to the create method.
 * TODO introduce 'mandatory' option
 */
abstract class Attribute implements IAttribute
{
    /**
     * Holds a reference to the attribute's type.
     *
     * @var IDocumentType $type;
     */
    protected $type;

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
     * Returns the attribute's type.
     *
     * @return IDocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the attribute's type once, if it isn't assigned.
     *
     * @param IDocumentType $type
     */
    public function setType(IDocumentType $type)
    {
        if (!$this->type) {
            $this->type = $type;
        }
        // @todo else throw an exception,
        // as a second call to setType might imply a logic error?
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
     * @return IValue
     */
    public function getDefaultValue()
    {
        return $this->getOption(self::OPTION_DEFAULT_VALUE, $this->getNullValue());
    }

    /**
     * Returns a attribute's null value.
     *
     * @return mixed
     */
    public function getNullValue()
    {
        return $this->getOption(self::OPTION_NULL_VALUE, null);
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
            $validator_implementor = $this->getOption(self::OPTION_VALIDATOR, $default_validator_class);

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
     * Creates a IValue, that is specific to the current attribute instance.
     *
     * @return IValue
     */
    public function createValue()
    {
        $implementor = $this->hasOption(self::OPTION_VALUE)
            ? $this->getOption(self::OPTION_VALUE)
            : $this->buildDefaultValueClassName();

        if (!class_exists($implementor)) {
            throw new RuntimeException(
                "Invalid attribute value-holder given upon createValue request."
            );
        }
        $value = new $implementor($this);

        if (!$value instanceof IValue) {
            throw new InvalidTypeException(
                sprintf(
                    "Invalid valueholder implementation '%s' given for attribute '%s'.",
                    $implementor,
                    $this->getName()
                )
            );
        }

        $value->set($this->getDefaultValue());

        return $value;
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
     * Returns the IValue implementation to use when aggregating (value)data for this attribute.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an IValue implementation.
     */
    protected function buildDefaultValueClassName()
    {
        $valueholder_namespace = "\\Dat0r\\Runtime\\Attribute\\Value\\Type";
        $attribute_classname_parts = explode('\\', get_class($this));
        $valueholder_class = array_pop($attribute_classname_parts) . 'Value';

        return $valueholder_namespace . '\\' . $valueholder_class;
    }
}
