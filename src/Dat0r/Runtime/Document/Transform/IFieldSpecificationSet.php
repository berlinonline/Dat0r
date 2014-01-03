<?php

namespace Dat0r\Runtime\Document\Transform;

interface IFieldSpecificationSet
{
    public function getName();

    public function getOptions();

    public function getFieldSpecifications();
}
