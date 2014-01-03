<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Options;
use Dat0r\Runtime\Document\IDocument;

interface ITransformer
{
    /**
     * @param IDocument $document
     * @param IFieldSpecifications $field_specs
     *
     * @return array
     */
    public function transform(IDocument $document, IFieldSpecifications $field_specs);

    /**
     * @param array $data
     * @param IDocument $document
     * @param IFieldSpecifications $field_specs
     *
     * @return void
     */
    public function transformBack(array $data, IDocument $document, IFieldSpecifications $field_specs);

    /**
     * @return Options
     */
    public function getOptions();
}
