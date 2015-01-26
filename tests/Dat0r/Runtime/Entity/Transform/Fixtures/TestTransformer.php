<?php

namespace Dat0r\Tests\Runtime\Entity\Transform\Fixtures;

use Dat0r\Tests\TestCase;
use Dat0r\Common\Options;
use Dat0r\Runtime\Entity\Transform\Transformer;

/**
 * An Transformer base implementation as would be created by the code-generation.
 */
class TestTransformer extends Transformer
{
    public function __construct(array $state = array())
    {
        parent::__construct(
            array_merge(
                $state,
                array(
                    'options' => array(
                        'foo' => 'bar',
                    )
                )
            )
        );
    }
}
