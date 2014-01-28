<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Configurable;

class FieldSpecifications extends Configurable implements IFieldSpecifications
{
    /**
     * @var string $name
     */
    protected $name;

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
        } elseif (is_array($field_specification_map)) {
            $this->field_specification_map = FieldSpecificationMap::create();
            foreach ($field_specification_map as $spec_key => $field_specification) {
                if ($field_specification instanceof IFieldSpecification) {
                    $this->field_specification_map->setItem($spec_key, $field_specification);
                } else {
                    $this->field_specification_map->setItem(
                        $spec_key,
                        FieldSpecification::create($field_specification)
                    );
                }
            }
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'FieldSpecificationMap' and 'array' are supported."
            );
        }
    }
}
