<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Entity\Options;
use Dat0r\Runtime\Document\IDocument;

interface ITransformer
{
    /**
     * @param IDocument $document
     * @param ISpecificationContainer $spec_container
     *
     * @return array
     */
    public function transform(IDocument $document, ISpecificationContainer $spec_container);

    /**
     * @param array $data
     * @param IDocument $document
     * @param ISpecificationContainer $spec_container
     *
     * @return void
     */
    public function transformBack(array $data, IDocument $document, ISpecificationContainer $spec_container);

    /**
     * @return Options
     */
    public function getOptions();
}
