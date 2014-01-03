<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Options;
use Dat0r\Runtime\Document\IDocument;

interface ITransformer
{
    /**
     * @return IFieldSpecifications
     */
    public function getFieldSpecifications();

    /**
     * @return Options
     */
    public function getOptions();

    /**
     * @param IDocument $document
     *
     * @return array
     */
    public function transform(IDocument $document);

    /**
     * @param array $data
     * @param IDocument $document
     *
     * @return void
     */
    public function transformBack(array $data, IDocument $document);
}
