<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Options;
use Dat0r\Runtime\Document\IDocument;

interface ITransformer
{
    /**
     * @return IFieldSpecSet
     */
    public function getFieldSpecSet();

    /**
     * @return Options
     */
    public function getOptions();

    /**
     * @param IDocument $document
     *
     * @return mixed
     */
    public function transform(IDocument $document);
}
