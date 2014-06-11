<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Validator\IValidator;
use Dat0r\Runtime\Module\IModule;

/**
 * IFields hold meta data that is used to model document properties,
 * hence your data's behaviour concerning consistent containment.
 */
interface IField
{
    /**
     * Returns the name of the field.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the field's module.
     *
     * @return IModule
     */
    public function getModule();

    /**
     * Sets the field's module once, if it isn't assigned.
     *
     * @param IModule $module
     */
    public function setModule(IModule $module);

    /**
     * Returns the field's parent, if it has one.
     *
     * @return IField
     */
    public function getParent();

    /**
     * Sets the field's parent once, if it isn't yet assigned.
     *
     * @param IField $parent
     */
    public function setParent(IField $parent);

    /**
     * Returns the default value of the field.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Returns a field's null value.
     *
     * @return mixed
     */
    public function getNullValue();

    /**
     * Returns a field option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Tells if a field currently owns a specific option.
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
     * Creates a IValueHolder instance dedicated to the current field instance.
     *
     * @return IValueHolder
     */
    public function createValueHolder();
}
