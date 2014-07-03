<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Parameters;

interface ISpecificationContainer
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return SpecificationMap
     */
    public function getSpecificationMap();

    /**
     * @return Parameters
     */
    public function getParameters();
}
