<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\Configurable;

class Specification extends Configurable implements SpecificationInterface
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
