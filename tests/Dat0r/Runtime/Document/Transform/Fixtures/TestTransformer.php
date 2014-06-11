<?php

namespace Dat0r\Tests\Runtime\Document\Transform\Fixtures;

use Dat0r\Tests\TestCase;
use Dat0r\Common\Options;
use Dat0r\Runtime\Document\Transform\Transformer;

/**
 * An Transformer base implementation as would be created by the code-generation.
 */
class TestTransformer extends Transformer
{
    public static function create(array $data = array())
    {
        return parent::create(
            array(
                'options' => new Options(
                    array(
                        'foo' => 'bar',
                    )
                )
            )
        );
    }
}
