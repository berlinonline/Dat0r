<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Options;

interface IFieldSpecSet
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
     * @return FieldSpecMap
     */
    public function getFieldSpecs();
}
