<?php

namespace Dat0r\Tests\CodeGen\Parser;

use Dat0r\Tests\TestCase;
use Dat0r\CodeGen\Schema\OptionDefinitionList;
use Dat0r\CodeGen\Schema\OptionDefinition;

class OptionDefinitionListTest extends TestCase
{
    public function testToArray()
    {
        $nested_options_list = new OptionDefinitionList();
        $nested_options_list->addItem(
            new OptionDefinition(
                array('value' => 'Nested Foobar One')
            )
        );

        $list = new OptionDefinitionList();
        $list->addItem(
            new OptionDefinition(
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
