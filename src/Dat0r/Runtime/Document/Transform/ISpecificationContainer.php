<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Options;

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
     * @return Options
     */
    public function getOptions();
}
