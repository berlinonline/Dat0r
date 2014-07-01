<?php

namespace Dat0r\Tests\Runtime\Document\Transform\Fixtures;

use Dat0r\Tests\TestCase;
use Dat0r\Common\Options;
use Dat0r\Runtime\Document\Transform\Transformer;
use Dat0r\Runtime\Document\Transform\SpecificationContainer;
use Dat0r\Runtime\Document\Transform\SpecificationMap;
use Dat0r\Runtime\Document\Transform\Specification;

/**
 * An AttributeSpecifications base implementation as would be created by the code-generation.
 */
class EmbedSpecifications extends SpecificationContainer
{
    public function __construct(array $state = array())
    {
        $specification_map = new SpecificationMap();
        $specification_map->setItems(
            array(
                'title' => new Specification(
                    array(
                        'name' => 'title',
                        'options' => array(
                            'attribute' => 'headline'
                        )
                    )
                ),
                'author' => new Specification(
                    array(
                        'name' => 'author'
                    )
                )
            )
        );

        return parent::__construct(
            array(
                'name' => 'embed',
                'options' => array(),
                'specification_map' => $specification_map
            )
        );
    }
}
