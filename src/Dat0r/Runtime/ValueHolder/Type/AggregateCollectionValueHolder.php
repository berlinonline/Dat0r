<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Document\DocumentList;

/**
 * Default IValueHolder implementation for the AggregateCollection.
 * Holds an aggregate attribute's documents in form of a DocumentList.
 */
class AggregateCollectionValueHolder extends ValueHolder
{
    /**
     * An  aggregate's value can not be greater than an other aggregate attribute's value.
     *
     * @param mixed $righthand_value
     *
     * @return boolean (always false)
     *
     * @todo Throw something like an UnsupportedOperationException instead of returning false here?
     *       Same for the isValueLessThan method.
     */
    public function isValueGreaterThan($righthand_value)
    {
        return false;
    }

    /**
     * An  aggregate's value can not be less than an other aggregate attribute's value.
     *
     * @param mixed $righthand_value
     *
     * @return boolean (always false)
     */
    public function isValueLessThan($righthand_value)
    {
        return false;
    }

    /**
     * Tells if a given document list contains the same documents.
     * The list is considered equal when documents with the same values occur in the same order
     * as in the valueholder's local value (DocumentList).
     *
     * @param mixed $righthand_value
     *
     * @return boolean
     */
    public function isValueEqualTo($righthand_value)
    {
        $lefthand_docs = $this->getValue();
        $is_equal = true;

        if (count($lefthand_docs) !== count($righthand_value)) {
            $is_equal = false;
        } else {
            foreach ($lefthand_docs as $index => $document) {
                if (!$document->isEqualTo($righthand_value->getItem($index))) {
                    $is_equal = false;
                }
            }
        }

        return $is_equal;
    }
}
