<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Options;

interface IFieldSpecifications
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return Options
     */
    public function getOptions();

    /**
     * @return FieldSpecificationMap
     */
    public function getFieldSpecificationMap();
}
