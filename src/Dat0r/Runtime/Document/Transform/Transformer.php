<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Configurable;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Document\IDocument;

class Transformer extends Configurable implements ITransformer
{
    /**
     * @var IFieldSpecifications $field_specifications
     */
    protected $field_specifications;

    /**
     * @return IFieldSpecifications
     */
    public function getFieldSpecifications()
    {
        return $this->field_specifications;
    }

    /**
     * @param IDocument $document
     *
     * @return array
     */
    public function transform(IDocument $document)
    {
        $field_specification_map = $this->getFieldSpecifications()->getFieldSpecificationMap();

        $transformed_data = array();
        foreach ($field_specification_map as $output_key => $field_specification) {
            $transformed_data[$output_key] = $this->transformValue($field_specification, $document);
        }

        return $transformed_data;
    }

    /**
     * @param array $data
     * @param IDocument $document
     *
     * @return void
     */
    public function transformBack(array $data, IDocument $document)
    {
        $field_specification_map = $this->getFieldSpecifications()->getFieldSpecificationMap();

        $transformed_data = array();
        foreach ($field_specification_map as $output_key => $field_specification) {
            if (array_key_exists($data, $output_key)) {
                $incoming_value = $data[$output_key];
                $transformed_data[$fieldname] = $this->transformValueBack($field_specification, $document, $incoming_value);
            }
        }

        $document->setValues($transformed_data);
    }

    protected function transformValue(IFieldSpecification $field_specification, IDocument $document)
    {
        $fieldname = $field_specification->getOption('field', $field_specification->getName());
        $document_value = $document->getValue($fieldname);
// @todo Implement!
        return $document_value;
    }

    protected function transformValueBack(IFieldSpecification $field_specification, IDocument $document, $value)
    {
// @todo Implement!
        return $value;
    }

    /**
     * @param mixed $field_specifications Either 'IFieldSpecifications' instance or array suitable for creating one.
     */
    protected function setFieldSpecifications($field_specifications)
    {
        if ($field_specifications instanceof IFieldSpecifications) {
            $this->field_specifications = $field_specifications;
        } else if (is_array($field_specifications)) {
            $this->field_specifications = FieldSpecifications::create($field_specifications);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'IFieldSpecifications' and 'array' are supported."
            );
        }
    }
}
