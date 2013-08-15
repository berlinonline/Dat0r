<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Freezable;
use Dat0r\Core\Field\IField;

/**
 * Basic IValueHolder implementation that all other ValueHolders should inherit from.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class ValueHolder extends Freezable implements IValueHolder
{
    /**
     * @var IField $field Holds field which's data we are handling.
     */
    private $field;

    /**
     * @var mixed $value Holds the ValueHolder's value.
     */
    private $value;

    /**
     * Creates a new IValueHolder instance from a given value.
     *
     * @param IField $field
     * @param mixed $value
     *
     * @return IValueHolder
     */
    public static function create(IField $field, $value = null)
    {
        return new static($field, $value);
    }

    /**
     * Returns the ValueHolder's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the ValueHolder's value.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        if ($this->isFrozen()) {
            throw new Error\ObjectImmutableException(
                "Trying to set value on a frozen IValueHolder instance."
            );
        }
        $this->value = $value;
    }

    /**
     * Returns string representation of the current value.
     *
     * @return string
     */
    public function __toString()
    {
        $value = $this->getValue();
        $string = '';

        if (is_object($value)) {
            if (is_callable(array($value, '__toString'))) {
                $string = $value->__toString();
            } elseif (is_callable(array($value, 'toArray'))) {
                $string = sprintf(
                    '(%s) as %s',
                    get_class($value),
                    print_r($value->toArray(), true)
                );
            } else {
                $string = sprintf('(%s)', get_class($value));
            }
        } else {
            $string = print_r($value, true);
        }

        return $string;
    }

    /**
     * Contructs a new ValueHolder instance from a given value.
     *
     * @param IField $field
     * @param mixed $value
     */
    protected function __construct(IField $field, $value = null)
    {
        $this->field = $field;

        if (null !== $value) {
            $this->setValue($value);
        }
    }

    /**
     * Returns the field that we are handling the data for.
     *
     * @return IField
     */
    protected function getField()
    {
        return $this->field;
    }
}
