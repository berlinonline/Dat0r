<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Document\Transform\FieldSpecifications;

class FieldSpecificationsTest extends TestCase
{
    public function testCreate()
    {
        $field_specifications = FieldSpecifications::create($this->getExampleFieldSpec());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpecifications', $field_specifications);
        $this->assertEquals('embed', $field_specifications->getName());

        $options = $field_specifications->getOptions();
        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $options);
        $this->assertEquals(array('foo' => 'bar', 'blah' => 'blub'), $options->toArray());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\FieldSpecificationMap', $field_specifications->getFieldSpecificationMap());
    }

    protected function getExampleFieldSpec()
    {
        return array(
            'name' => 'embed',
            'options' => array(
                'foo' => 'bar',
                'blah' => 'blub'
            ),
            'field_specification_map' => array(
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
