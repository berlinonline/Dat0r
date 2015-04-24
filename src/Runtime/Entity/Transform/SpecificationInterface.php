<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\ConfigurableInterface;
use Dat0r\Common\Object;

interface SpecificationInterface extends ConfigurableInterface
{
    /**
     * @return string
     */
    public function getName();
}
