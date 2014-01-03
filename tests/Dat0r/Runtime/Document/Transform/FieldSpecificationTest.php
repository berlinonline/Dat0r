<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Document\Transform\FieldSpecification;

class FieldSpecificationTest extends TestCase
{
    public function testCreate()
    {
        $fs = FieldSpecification::create($this->getExampleFieldSpec());

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpecification', $fs);
        $this->assertEquals('bar', $fs->getName());

        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $fs->getOptions());
        $this->assertEquals('foo', $fs->getOptions()->get('map_as', 'default'));
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
