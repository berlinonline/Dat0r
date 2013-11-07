<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class OptionDefinitionList extends TypedList implements IUniqueCollection
{
    public function filterByName($name)
    {
        $options = $this->filter(
            function ($option) use ($name) {
                return $option->getName() === $name;
            }
        );

        return count($options) > 0 ? $options[0] : null;
    }

    public function toArray()
    {
        $data = array();

        foreach ($this->items as $option) {
            $name = $option->getName();
            $value = $option->getValue();
            $next_value = $value;

            if ($value instanceof OptionDefinitionList) {
                $next_value = $value->toArray();
            }

            $next_value = $next_value ? $next_value : $option->getDefault();

            if ($name) {
                $data[$name] = $next_value;
            } else {
                $data[] = $next_value;
            }
        }

        return $data;
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Schema\\OptionDefinition';
    }
}
