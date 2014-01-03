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
    public function transform(IDocument $document, IFieldSpecifications $field_specs)
    {
        $specifications_map = $field_specs->getFieldSpecificationMap();
        $transformation = Transformation::create();

        $transformed_data = array();
        foreach ($specifications_map as $output_key => $field_specification) {
            $transformed_data[$output_key] = $transformation->apply($document, $field_specification);
        }

        return $transformed_data;
    }

    /**
     * @param array $data
     * @param IDocument $document
     *
     * @return void
     */
    public function transformBack(array $data, IDocument $document, IFieldSpecifications $field_specs)
    {
        $field_specification_map = $field_specs->getFieldSpecificationMap();
        $transformation = Transformation::create();

        $transformed_data = array();
        foreach ($field_specification_map as $output_key => $field_specification) {
            if (array_key_exists($data, $output_key)) {
                $transformation->revert($document, $field_specification, $data[$output_key]);
            }
        }
    }
}
