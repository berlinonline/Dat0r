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
     * @return mixed
     */
    public function transform(IDocument $document)
    {
        // @todo transform stuff based on fieldset information
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
