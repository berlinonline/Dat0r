<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Configurable;

class FieldSpecification extends Configurable implements IFieldSpecification
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
