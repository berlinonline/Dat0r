<?php

namespace Dat0r\Runtime\Attribute\TextList;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for text collection value containment.
 */
class TextListValueHolder extends ValueHolder
{
    /**
     * Tells whether a specific ValueHolderInterface instance's value is considered equal to
     * the value of an other given ValueHolderInterface.
     *
     * @param ValueHolderInterface $other_value
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        /** @var array $lefthand_value */
        $lefthand_value = $this->get();
        $lefthand_count = 0;
        $righthand_count = 0;
        $are_equal = true;

        if (is_array($lefthand_value)) {
            $lefthand_count = count($lefthand_value);
        }
        if (is_array($other_value)) {
            $righthand_count = count($other_value);
        }

        if (0 < $lefthand_count && $lefthand_count === $righthand_count) {
            foreach ($lefthand_value as $idx => $text) {
                if ($other_value[$idx] !== $text) {
                    $are_equal = false;
                }
            }
        } elseif ($lefthand_count !== $righthand_count) {
            $are_equal = false;
        }

        return $are_equal;
    }

    /**
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function set($value)
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

        return parent::set($values);
    }
}
