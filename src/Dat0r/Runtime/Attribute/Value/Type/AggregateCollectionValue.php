<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Document\DocumentList;

/**
 * Default IValue implementation for the AggregateCollection.
 * Holds an aggregate attribute's documents in form of a DocumentList.
 */
class AggregateCollectionValue extends Value
{
    /**
     * Tells if a given document list contains the same documents.
     * The list is considered equal when documents with the same values occur in the same order
     * as in the valueholder's local value (DocumentList).
     *
     * @param mixed $other_value
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        $lefthand_docs = $this->get();
        $is_equal = true;

        if (count($lefthand_docs) !== count($other_value)) {
            $is_equal = false;
        } else {
            foreach ($lefthand_docs as $index => $document) {
                if (!$document->isEqualTo($other_value->getItem($index))) {
                    $is_equal = false;
                }
            }
        }

        return $is_equal;
    }
}
