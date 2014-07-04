<?php

namespace Dat0r\Common;

use Params\Immutable\ImmutableParameters;
use Params\Immutable\ImmutableParametersTrait;

class Parameters implements IParameters
{
    use ImmutableParametersTrait;

    /**
     * @param array $config associative array with configuration parameters
     */
    public function __construct(array $config = array())
    {
        $this->parameters = new ImmutableParameters($config);
    }
}

