<?php

namespace Dat0r\Runtime\Attribute\Float;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for float value containment.
 */
class FloatValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param float $other_value number value to compare
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!is_float($other_value)) {
            return false;
        }

        if (abs($this->getValue()-$other_value) < $this->getPrecision()) {
            return true;
        }

        return false;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     * BEWARE that allowing INF or NAN values via attribute options leads to
     * this method returning INF/NAN floats as string.
     *
     * @return float value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        $allow_infinity = $this->getAttribute()->getOption(FloatAttribute::OPTION_ALLOW_INFINITY, false);
        if (is_infinite($this->getValue())) {
            return (string)$this->getValue();
        }

        $allow_nan = $this->getAttribute()->getOption(FloatAttribute::OPTION_ALLOW_NAN, false);
        if (is_nan($this->getValue())) {
            return (string)$this->getValue();
        }

        return $this->getValue();
    }

    /**
     * Returns the type of the value that is returned for the toNative() call.
     * This is used for typehints in code generation and might be used in other
     * layers (e.g. web form submissions) to handle things differently.
     * BEWARE that allowing INF or NAN values via attribute options leads to
     * the toNative() method returning INF/NAN floats as type string.
     *
     * @return string return type of the toNative() method
     */
    public function getNativeType()
    {
        return 'float';
    }

    /**
     * Returns the type of the internal value of the valueholder. This can
     * be anything from 'string', 'array' or 'int' to a fully qualified class
     * name of the value object or PHP object used for storage internally.
     *
     * The returned type is the one returned by getValue() method calls.
     *
     * @return string type or FQCN of the internal value
     */
    public function getValueType()
    {
        return 'float';
    }

    /**
     * @return float value that can be used as delta/epsilon for float value equality comparisons
     */
    protected function getPrecision()
    {
        $php_precision_value = ini_get('precision');
        $php_precision = filter_var($php_precision_value, FILTER_VALIDATE_INT);
        if ($php_precision === false || $php_precision_value === true) {
            trigger_error("The default PHP ini setting 'precision' is not interpretable as integer.", E_USER_NOTICE);
            $php_precision = 14;
        }

        $precision_digits_value = $this->getAttribute()->getOption(
            FloatAttribute::OPTION_PRECISION_DIGITS,
            $php_precision
        );
        $precision_digits = filter_var($precision_digits_value, FILTER_VALIDATE_INT);
        if ($precision_digits === false || $precision_digits_value === true) {
            trigger_error(
                "The configured number of digits for float precision is not interpretable as integer. " .
                "Using fallback of 14 digits.",
                E_USER_WARNING
            );
            $precision_digits = 14;
        }

        if ($php_precision < $precision_digits) {
            throw new RuntimeException(
                sprintf(
                    'PHP ini setting "precision" (="%s") is set to a lower value than the wanted ' .
                    'precision (="%s") for float values of attribute "%s". Change either setting.',
                    $php_precision_value,
                    $precision_digits_value,
                    $this->getAttribute()->getName()
                )
            );
        }

        $precision = filter_var("1e-".abs($precision_digits), FILTER_VALIDATE_FLOAT);
        if ($precision === false || $precision_digits === true) {
            throw new InvalidConfigException(
                "Could not interprete float precision digits correctly. Please specify a positive integer (e.g. 16)."
            );
        }

        return $precision;
    }
}
