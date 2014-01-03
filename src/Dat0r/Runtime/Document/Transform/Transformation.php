<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Configurable;
use Dat0r\Runtime\Document\IDocument;

class Transformation extends Configurable implements ITransformation
{
    /**
     * Transform the document value, which is described by the given fieldspec,
     * to it's output representation.
     *
     * @param IDocument $document
     * @param IFieldSpecification $field_spec
     *
     * @return mixed
     */
    public function apply(IDocument $document, IFieldSpecification $field_spec)
    {
        $fieldname = $field_spec->getOption('field', $field_spec->getName());
        $document_value = $document->getValue($fieldname);

        return $document_value;
    }

    /**
     * Transform an incoming value, which is described by the given fieldspec,
     * to it's input (document compatible) representation and set result on the given document.
     *
     * @param mixed $input_value
     * @param IDocument $document
     * @param IFieldSpecification $field_spec
     *
     * @return void
     */
    public function revert($input_value, IDocument $document, IFieldSpecification $field_spec)
    {
        $fieldname = $field_spec->getOption('field', $field_spec->getName());
        $document->setValue($fieldname, $input_value);
    }
}
