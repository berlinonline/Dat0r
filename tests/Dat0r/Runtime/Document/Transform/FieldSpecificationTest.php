<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Document\Transform\FieldSpecification;

class FieldSpecificationTest extends TestCase
{
    public function testCreate()
    {
        $field_specification = FieldSpecification::create($this->getExampleFieldSpec());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpecification', $field_specification);
        $this->assertEquals('bar', $field_specification->getName());

        $options = $field_specification->getOptions();
        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $options);
        $this->assertEquals('foo', $options->get('map_as', 'default'));
    }

    protected function getExampleFieldSpec()
    {
        return array(
            'name' => 'bar',
            'options' => array(
                'map_as' => 'foo'
            )
        );
    }
}
