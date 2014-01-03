<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Runtime\Document\IDocument;

interface ITransformation
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
    public function apply(IDocument $document, IFieldSpecification $field_spec);

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
    public function revert($input_value, IDocument $document, IFieldSpecification $field_spec);

    /**
     * @return Options
     */
    public function getOptions();
}
