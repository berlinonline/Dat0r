<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Options;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Document\IDocument;

class Transformer extends Object implements ITransformer
{
    /**
     * @var IFieldSpecSet $field_spec_set
     */
    protected $field_spec_set;

    /**
     * @var Options $options
     */
    protected $options;

    /**
     * @return IFieldSpecSet
     */
    public function getFieldSpecSet()
    {
        return $this->field_spec_set;
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
     * @param mixed $field_spec_set Either 'IFieldSpecSet' instance or array suitable for creating one.
     */
    protected function setFieldSpecSet($field_spec_set)
    {
        if ($field_spec_set instanceof IFieldSpecSet) {
            $this->field_spec_set = $field_spec_set;
        } else if (is_array($field_spec_set)) {
            $this->field_spec_set = FieldSpecSet::create($field_spec_set);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'IFieldSpecSet' and 'array' are supported."
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
