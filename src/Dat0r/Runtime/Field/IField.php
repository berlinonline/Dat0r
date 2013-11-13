<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Validator\IValidator;

/**
 * IFields hold meta data that is used to model document properties,
 * hence your data's behaviour concerning consistent containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IField
{
    /**
     * Creates a new field instance.
     *
     * @param string $name
     * @param array $options
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
     * Returns the default value of the field.
     *
     * @return mixed
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
