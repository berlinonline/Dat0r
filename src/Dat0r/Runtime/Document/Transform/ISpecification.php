<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\IConfigurable;

interface ISpecification extends IConfigurable
{
    /**
     * @return string
     */
    public function getName();
}
