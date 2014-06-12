<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Document\Transform\SpecificationContainer;

class SpecificationContainerTest extends TestCase
{
    public function testCreate()
    {
        $spec_container = SpecificationContainer::create($this->getExampleSpec());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\ISpecificationContainer', $spec_container);
        $this->assertEquals('embed', $spec_container->getName());

        $options = $spec_container->getOptions();
        $this->assertInstanceOf('\\Dat0r\\Common\\Entity\\Options', $options);
        $this->assertEquals(array('foo' => 'bar', 'blah' => 'blub'), $options->toArray());

        $this->assertInstanceOf(
            '\\Dat0r\\Runtime\\Document\\Transform\\SpecificationMap',
            $spec_container->getSpecificationMap()
        );
    }

    protected function getExampleSpec()
    {
        return array(
            'name' => 'embed',
            'options' => array(
                'foo' => 'bar',
                'blah' => 'blub'
            ),
            'specification_map' => array(
                'voting_stats' => array(
                    'name' => 'voting_stats',
                    'options' => array(
                        'map_as' => 'voting_average',
                        'value' => 'expression:"foo" ~ "BAR"',
                        'getter' => 'getVotingAverage',
                        'setter' => 'setVotingAverage',
                        'input' => false,
                        'output' => true
                    )
                )
            )
        );
    }
}
