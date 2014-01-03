<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Runtime\Document\IDocument;

interface ITransformer
{
    /**
     * @return IFieldSpecificationSet
     */
    public function getFieldSpecificationSet();

    /**
     * @param IDocument $document
     *
     * @return mixed
     */
    public function transform(IDocument $document);
}
