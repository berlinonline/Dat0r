<?php

namespace Dat0r\Tests\Runtime\Document\Transform\Fixtures;

use Dat0r\Tests\TestCase;
use Dat0r\Common\Entity\Options;
use Dat0r\Runtime\Document\Transform\Transformer;
use Dat0r\Runtime\Document\Transform\SpecificationContainer;
use Dat0r\Runtime\Document\Transform\SpecificationMap;
use Dat0r\Runtime\Document\Transform\Specification;

/**
 * An AttributeSpecifications base implementation as would be created by the code-generation.
 */
class EmbedSpecifications extends SpecificationContainer
{
    public static function create(array $data = array())
    {
        return parent::create(
            array(
                'name' => 'embed',
                'options' => new Options(),
                'specification_map' => SpecificationMap::create(
                    array(
                        'items' => array(
                            'title' => Specification::create(
                                array(
                                    'name' => 'title',
                                    'options' => new Options(
                                        array(
                                            'attribute' => 'headline'
                                        )
                                    )
                                )
                            ),
                            'author' => Specification::create(
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
