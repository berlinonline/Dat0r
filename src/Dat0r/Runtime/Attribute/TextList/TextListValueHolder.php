<?php

namespace Dat0r\Runtime\Attribute\TextList;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for text collection value containment.
 */
class TextListValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param array $other_value values to compare to the internal ones
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!is_array($other_value)) {
            return false;
        }

        /** @var array $texts */
        $texts = $this->getValue();

        $texts_count = count($texts);
        $other_count = count($other_value);

        if ($texts_count !== $other_count) {
            return false;
        }

        foreach ($texts as $idx => $val) {
            if ($other_value[$idx] !== $val) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        return $this->getValue();
    }

    /**
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        // @todo move to validator
        $values = array();
        $value = !is_array($value) || empty($value) ? array() : $value;
        foreach ($value as $text) {
            $text = trim((string)$text);
            if (!empty($text)) {
                $values[] = $text;
            }
        }

        return parent::setValue($values);
    }
}
