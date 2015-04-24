<?php

namespace Dat0r\Tests\Runtime\Entity;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Entity\Transform\Specification;

class SpecificationTest extends TestCase
{
    public function testCreate()
    {
        $specification = new Specification($this->getExampleSpec());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Entity\\Transform\\SpecificationInterface', $specification);
        $this->assertEquals('bar', $specification->getName());

        $options = $specification->getOptions();
        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $options);
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
