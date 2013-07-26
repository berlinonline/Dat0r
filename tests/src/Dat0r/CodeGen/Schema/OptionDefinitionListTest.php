<?php

namespace Dat0r\Tests\CodeGen\Parser;

use Dat0r\Tests;
use Dat0r\CodeGen\Schema;

class OptionDefinitionListTest extends Tests\TestCase
{
    public function testToArray()
    {
        $nested_options_list = Schema\OptionDefinitionList::create();
        $nested_options_list->add(
            Schema\OptionDefinition::create(
                array('value' => 'Nested Foobar One')
            )
        );

        $list = Schema\OptionDefinitionList::create();
        $list->add(
            Schema\OptionDefinition::create(
                array(
                    'name' => 'Parent Foobar',
                    'value' => $nested_options_list
                )
            )
        );

        $expected_list_array = array(
            'Parent Foobar' => array('Nested Foobar One')
        );

        $this->assertSame($expected_list_array, $list->toArray());
    }
}
