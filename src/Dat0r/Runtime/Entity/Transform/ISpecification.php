<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\ConfigurableInterface;

interface ISpecification extends ConfigurableInterface
{
    /**
     * @return string
     */
    public function getName();
}
