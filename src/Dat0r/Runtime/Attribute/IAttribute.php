<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Runtime\Attribute\ValueHolder\IValueHolder;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Validator\IValidator;
use Dat0r\Runtime\IDocumentType;

/**
 * IAttributes hold meta data that is used to model document properties,
 * hence your data's behaviour concerning consistent containment.
 */
interface IAttribute
{
    /**
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the attribute's type.
     *
     * @return IDocumentType
     */
    public function getType();

    /**
     * Sets the attribute's type once, if it isn't assigned.
     *
     * @param IDocumentType $type
     */
    public function setType(IDocumentType $type);

    /**
     * Returns the attribute's parent, if it has one.
     *
     * @return IAttribute
     */
    public function getParent();

    /**
     * Sets the attribute's parent once, if it isn't yet assigned.
     *
     * @param IAttribute $parent
     */
    public function setParent(IAttribute $parent);

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
     * @return IValidator
     */
    public function getValidator();

    /**
     * Creates a IValueHolder instance dedicated to the current attribute instance.
     *
     * @return IValueHolder
     */
    public function createValueHolder();
}
