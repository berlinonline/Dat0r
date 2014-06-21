<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Configurable;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Document\IDocument;

class Transformer extends Configurable implements ITransformer
{
    /**
     * @param IDocument $document
     *
     * @return array
     */
    public function transform(IDocument $document, ISpecificationContainer $spec_container)
    {
        $specification_map = $spec_container->getSpecificationMap();
        $transformation = new Transformation();

        $transformed_data = array();
        foreach ($specification_map as $output_key => $specification) {
            $transformed_data[$output_key] = $transformation->apply($document, $specification);
        }

        return $transformed_data;
    }

    /**
     * @param array $data
     * @param IDocument $document
     *
     * @return void
     */
    public function transformBack(array $data, IDocument $document, ISpecificationContainer $spec_container)
    {
        $specification_map = $spec_container->getSpecificationMap();
        $transformation = new Transformation();

        foreach ($specification_map as $output_key => $specification) {
            if (array_key_exists($data, $output_key)) {
                $transformation->revert($document, $specification, $data[$output_key]);
            }
        }
    }
}
