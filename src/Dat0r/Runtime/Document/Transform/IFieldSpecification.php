<?php

namespace Dat0r\Runtime\Document\Transform;

interface IFieldSpecification
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
