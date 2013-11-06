<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Runtime\IFreezable;
use Dat0r\Runtime\Field\IField;

/**
 * @todo Explain what valid holders are and what they are supposed to do.
 */
interface IValueHolder extends IFreezable
{
    /**
     * Creates a new IValueHolder instance for the given value.
     *
     * @param IField $field
     * @param mixed $value
     *
     * @return IValueHolder
     */
    public static function create(IField $field, $value = null);

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
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan(IValueHolder $other);

    /**
     * Tells whether a given IValueHolder is considered being less than a given other IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other);

    /**
     * Tells whether a given IValueHolder is considered being equal to a given other IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo(IValueHolder $other);
}
