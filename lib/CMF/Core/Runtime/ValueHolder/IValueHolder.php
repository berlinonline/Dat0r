<?php

namespace CMF\Core\Runtime\ValueHolder;

use CMF\Core\Runtime;
use CMF\Core\Runtime\Field;

/**
 * @todo Explain what valid holders are and what they are supposed to do.
 */
interface IValueHolder extends Runtime\IFreezable
{
    /**
     * Creates a new IValueHolder instance for the given value.
     *
     * @param mixed $value
     *
     * @return CMF\Core\Runtime\ValueHolder\IValueHolder
     */
    public static function create(Field\IField $field, $value = NULL);

    /**
     * Returns the value holder's aggregated value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Sets the value holder's value.
     *
     * @param string $value
     */
    public function setValue($value);

    /** 
     * Tells whether a given IValueHolder is considered being less than a given other IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan(IValueHolder $other);

    /** 
     * Tells whether a given IValueHolder is considered being less than a given other IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other);

    /** 
     * Tells whether a given IValueHolder is considered being equal to a given other IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo(IValueHolder $other);
}
