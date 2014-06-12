<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Entity\Configurable;
use Dat0r\Runtime\Document\IDocument;

class Transformation extends Configurable implements ITransformation
{
    /**
     * Transform the document value, which is described by the given attributespec,
     * to it's output representation.
     *
     * @param IDocument $document
     * @param ISpecification $specification
     *
     * @return mixed
     */
    public function apply(IDocument $document, ISpecification $specification)
    {
        $attribute_name = $specification->getOption('attribute', $specification->getName());
        $document_value = $document->getValue($attribute_name);

        return $document_value;
    }

    /**
     * Transform an incoming value, which is described by the given attributespec,
     * to it's input (document compatible) representation and set result on the given document.
     *
     * @param mixed $input_value
     * @param IDocument $document
     * @param ISpecification $specification
     *
     * @return void
     */
    public function revert($input_value, IDocument $document, ISpecification $specification)
    {
        $attribute_name = $specification->getOption('attribute', $specification->getName());
        $document->setValue($attribute_name, $input_value);
    }
}
