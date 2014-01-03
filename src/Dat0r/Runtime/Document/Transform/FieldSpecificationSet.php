<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;

class FieldSpecificationSet extends Object implements IFieldSpecificationSet
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var Options $options
     */
    protected $options;

    /**
     * @var FieldSpecificationMap $field_specifications
     */
    protected $field_specifications;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return FieldSpecificationMap
     */
    public function getFieldSpecifications()
    {
        return $this->field_specifications;
    }

    /**
     * @param mixed $field_specifications Either 'FieldSpecificationMap' instance or array suitable for creating one.
     */
    protected function setFieldSpecifications($field_specifications)
    {
        if ($field_specifications instanceof FieldSpecificationMap) {
            $this->field_specifications = $field_specifications;
        } else if (is_array($field_specifications)) {
            $this->field_specifications = FieldSpecificationMap::create();
            $this->field_specifications->setItems($field_specifications);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'FieldSpecificationMap' and 'array' are supported."
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
