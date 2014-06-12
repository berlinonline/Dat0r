<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Entity\Configurable;

class SpecificationContainer extends Configurable implements ISpecificationContainer
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var SpecificationMap $specification_map
     */
    protected $specification_map;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return SpecificationMap
     */
    public function getSpecificationMap()
    {
        return $this->specification_map;
    }

    /**
     * @param mixed $specification_map Either 'SpecificationMap' instance or array suitable for creating one.
     */
    protected function setSpecificationMap($specification_map)
    {
        if ($specification_map instanceof SpecificationMap) {
            $this->specification_map = $specification_map;
        } elseif (is_array($specification_map)) {
            $this->specification_map = SpecificationMap::create();
            foreach ($specification_map as $spec_key => $specification) {
                if ($specification instanceof ISpecification) {
                    $this->specification_map->setItem($spec_key, $specification);
                } else {
                    $this->specification_map->setItem(
                        $spec_key,
                        Specification::create($specification)
                    );
                }
            }
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'SpecificationMap' and 'array' are supported."
            );
        }
    }
}
