<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Options;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Document\IDocument;

class Transformer extends Object implements ITransformer
{
    /**
     * @var IFieldSpecifications $field_specifications
     */
    protected $field_specifications;

    /**
     * @var Options $options
     */
    protected $options;

    /**
     * @return IFieldSpecifications
     */
    public function getFieldSpecifications()
    {
        return $this->field_specifications;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
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
        foreach ($field_specification_map as $fieldname => $field_specification) {
            $output_key = $field_specification->getOption('output_key', $fieldname);
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
        foreach ($field_specification_map as $fieldname => $field_specification) {
            $output_key = $field_specification->getOption('output_key', $fieldname);
            if (array_key_exists($data, $output_key)) {
                $incoming_value = $data[$output_key];
                $transformed_data[$fieldname] = $this->transformValueBack($field_specification, $document, $incoming_value);
            }
        }

        $document->setValues($transformed_data);
    }

    protected function transformValue(IFieldSpecification $field_specification, IDocument $document)
    {
        // @todo Implement!
        $document_value = $document->getValue($field_specification->getName());

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

    /**
     * @param mixed $options Either 'Options' instance or array suitable for creating one.
     */
    protected function setOptions($options)
    {
        if ($options instanceof Options) {
            $this->options = $options;
        } else if (is_array($options)) {
            $this->options = new Options($options);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'Options' and 'array' are supported."
            );
        }
    }
}
