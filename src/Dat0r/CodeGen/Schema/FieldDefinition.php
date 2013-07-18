<?php

namespace Dat0r\CodeGen\Schema;

class FieldDefinition
{
    private $name;

    private $description;

    private $type;

    private $options;

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function toArray()
    {
        return array(
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'options' => $this->options->toArray()
        );
    }

    public static function create(array $data = array())
    {
        $field_definition = new static();

        foreach ($data as $key => $value)
        {
            if (property_exists($field_definition, $key))
            {
                $field_definition->$key = $value;
            }
        }

        return $field_definition;
    }

    protected function __construct() {}
}
