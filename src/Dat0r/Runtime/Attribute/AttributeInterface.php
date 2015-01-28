<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Runtime\Attribute\Value\ValueHolderInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Validator\ValidatorInterface;
use Dat0r\Runtime\EntityTypeInterface;

/**
 * AttributeInterfaces hold meta data that is used to model entity properties,
 * hence your data's behaviour concerning consistent containment.
 */
interface AttributeInterface
{
    const OPTION_DEFAULT_VALUE = 'default_value';
    const OPTION_NULL_VALUE = 'null_value';
    const OPTION_VALUE_HOLDER = 'value_holder';
    const OPTION_VALIDATOR = 'validator';

    /**
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the attribute's type.
     *
     * @return EntityTypeInterface
     */
    public function getType();

    /**
     * Sets the attribute's type once, if it isn't assigned.
     *
     * @param EntityTypeInterface $type
     */
    public function setType(EntityTypeInterface $type);

    /**
     * Returns the attribute's parent, if it has one.
     *
     * @return AttributeInterface
     */
    public function getParent();

    /**
     * Sets the attribute's parent once, if it isn't yet assigned.
     *
     * @param AttributeInterface $parent
     */
    public function setParent(AttributeInterface $parent);

    /**
     * Returns the default value of the attribute.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Returns a attribute's null value.
     *
     * @return mixed
     */
    public function getNullValue();

    /**
     * Returns a attribute option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Tells if a attribute currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name);

    /**
     * @return ValidatorInterface
     */
    public function getValidator();

    /**
     * Creates a ValueHolderInterface instance dedicated to the current attribute instance.
     *
     * @return ValueHolderInterface
     */
    public function createValueHolder();
}
