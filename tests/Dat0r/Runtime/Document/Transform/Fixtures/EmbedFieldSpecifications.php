<?php

namespace Dat0r\Tests\Runtime\Document\Transform\Fixtures;

use Dat0r\Tests\TestCase;
use Dat0r\Common\Options;
use Dat0r\Runtime\Document\Transform\Transformer;
use Dat0r\Runtime\Document\Transform\FieldSpecifications;
use Dat0r\Runtime\Document\Transform\FieldSpecificationMap;
use Dat0r\Runtime\Document\Transform\FieldSpecification;

/**
 * An FieldSpecifications base implementation as would be created by the code-generation.
 */
class EmbedFieldSpecifications extends FieldSpecifications
{
    public static function create(array $data = array())
    {
        return parent::create(
            array(
                'name' => 'embed',
                'options' => new Options(),
                'field_specification_map' => FieldSpecificationMap::create(
                    array(
                        'items' => array(
                            'title' => FieldSpecification::create(
                                array(
                                    'name' => 'title',
                                    'options' => new Options(
                                        array(
                                            'field' => 'headline'
                                        )
                                    )
                                )
                            ),
                            'author' => FieldSpecification::create(
                                array(
                                    'name' => 'author'
                                )
                            )
                        )
                    )
                )
            )
        );
    }
}
