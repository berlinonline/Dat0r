<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Document\IDocument;

class Transformer extends Object implements ITransformer
{
    /**
     * @var IFieldSpecificationSet $field_specification_set
     */
    protected $field_specification_set;

    /**
     * @var Options $options
     */
    protected $options;

    /**
     * @return IFieldSpecificationSet
     */
    public function getFieldSpecificationSet()
    {
        return $this->field_specification_set;
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
     * @return mixed
     */
    public function transform(IDocument $document)
    {
        // @todo transform stuff based on fieldset information
    }

    /**
     * @param mixed $field_specification_set Either 'IFieldSpecificationSet' instance or array suitable for creating one.
     */
    protected function setFieldSpecificationSet($field_specification_set)
    {
        if ($field_specification_set instanceof IFieldSpecificationSet) {
            $this->field_specification_set = $field_specification_set;
        } else if (is_array($field_specification_set)) {
            $this->field_specification_set = FieldSpecificationSet::create($field_specification_set);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'IFieldSpecificationSet' and 'array' are supported."
            );
        }
    }

    /**
     * @param mixed $options Either 'Options' instance or array suitable for creating one.
     */
    protected function setOptions($options)
    {
        /*
        if ($options instanceof Options) {
            $this->options = $options;
        } else if (is_array($options)) {
            // $this->options = new Options($options);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'FieldSpecificationMap' and 'array' are supported."
            );
        }
        */
    }
}
