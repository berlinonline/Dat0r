<?php

namespace Dat0r\Runtime\Document\Transform;

interface ITransformer
{
    public function getFieldSpecificationSet();

    public function transform(IDocument $document);
}
