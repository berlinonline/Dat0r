<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Document\Transform\Transformer;

class TransformerTest extends TestCase
{
    public function testCreate()
    {
        $t = Transformer::create($this->getExampleTransform());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\ITransformer', $t);

        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $t->getOptions());
        $this->assertEquals('bar', $t->getOptions()->get('foo', 'default'));

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpecifications', $t->getFieldSpecifications());
        $this->assertEquals('embed', $t->getFieldSpecifications()->getName());
    }

    protected function getExampleTransform()
    {
        return array(
            'options' => array(
                'foo' => 'bar'
            ),
            'field_specifications' => array(
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
            )
        );
    }
}
