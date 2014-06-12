<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Document\Transform\Specification;

class SpecificationTest extends TestCase
{
    public function testCreate()
    {
        $specification = Specification::create($this->getExampleSpec());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\ISpecification', $specification);
        $this->assertEquals('bar', $specification->getName());

        $options = $specification->getOptions();
        $this->assertInstanceOf('\\Dat0r\\Common\\Entity\\Options', $options);
        $this->assertEquals('foo', $options->get('map_as', 'default'));
    }

    protected function getExampleSpec()
    {
        return array(
            'name' => 'bar',
            'options' => array(
                'map_as' => 'foo'
            )
        );
    }
}
