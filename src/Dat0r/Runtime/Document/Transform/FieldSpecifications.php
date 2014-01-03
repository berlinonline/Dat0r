<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Options;

class FieldSpecifications extends Object implements IFieldSpecifications
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
     * @var FieldSpecificationMap $field_specification_map
     */
    protected $field_specification_map;

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
     * @return FieldSpecMap
     */
    public function getFieldSpecificationMap()
    {
        return $this->field_specification_map;
    }

    /**
     * @param mixed $field_specification_map Either 'FieldSpecMap' instance or array suitable for creating one.
     */
    protected function setFieldSpecificationMap($field_specification_map)
    {
        if ($field_specification_map instanceof FieldSpecificationMap) {
            $this->field_specification_map = $field_specification_map;
        } else if (is_array($field_specification_map)) {
            $this->field_specification_map = FieldSpecificationMap::create();
            foreach ($field_specification_map as $spec_key => $field_specification) {
                if ($field_specification instanceof IFieldSpecification) {
                    $this->field_specification_map->setItem($spec_key, $field_specification);
                } else {
                    $this->field_specification_map->setItem($spec_key, FieldSpecification::create($field_specification));
                }
            }
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
