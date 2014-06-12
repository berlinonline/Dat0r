<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Entity\Options;

interface ISpecification
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return Options
     */
    public function getOptions();
}
