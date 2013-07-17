<?php

namespace Dat0r\Core\Field;

use Dat0r\Core\IFreezable;
use Dat0r\Core\ValueHolder\IValueHolder;

/**
 * IFields hold meta data that is used to model document properties,
 * hence your data's behaviour concerning consistent containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IField extends IFreezable
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
     * Validates a given value with a strategy dedicated to the field.
     *
     * @param mixed $value
     * 
     * @return boolean
     */
    public function validate($value);

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
    public function getOption($name, $default = NULL);

    /**
     * Tells if a field currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name);

    /**
     * Creates a IValueHolder instance dedicated to the current field instance.
     *
     * @param mixed $value
     *
     * @return IValueHolder
     */
    public function createValueHolder($value);
}
