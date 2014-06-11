<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Module\IModule;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\Field\Type\ReferenceField;
use Dat0r\Common\Error\RuntimeException;

class FieldPath
{
    const PATH_DELIMITER = '.';

    public static function getFieldPath(IField $field)
    {
        $path_parts = array($field->getName());

        $current_field = $field->getParent();
        $current_module = $field->getModule();
        while (
            $current_field instanceof AggregateField
            || $current_field instanceof ReferenceField
        ) {
            $path_parts[] = $current_module->getPrefix();
            $path_parts[] = $current_field->getName();

            $current_module = $current_field->getModule();
            $current_field = $current_field->getParent();
        }

        return implode(self::PATH_DELIMITER, array_reverse($path_parts));
    }

    public static function getFieldByPath(IModule $module, $field_path)
    {
        $path_parts = explode(self::PATH_DELIMITER, $field_path);

        if ($path_parts % 2 === 0) {
            throw new RuntimeException(
                "Invalid fieldpath(fieldname) given. Fieldparts must be made up of " .
                "'fieldname.module_prefix.fieldname' parts with a single final fieldname."
            );
        }

        $path_tuples = array();
        $next_tuple = array();
        for ($i = 1; $i <= count($path_parts); $i++) {
            $next_tuple[] = $path_parts[$i - 1];
            if ($i % 2 === 0) {
                $path_tuples[] = $next_tuple;
                $next_tuple = array();
            }
        }

        $destination_field = end($path_parts);
        $current_module = $module;

        foreach ($path_tuples as $path_tuple) {
            $current_field = $current_module->getField($path_tuple[0]);
            if ($current_field instanceof AggregateField) {
                $current_module = $current_field->getAggregateModuleByPrefix($path_tuple[1]);
            } elseif ($current_field instanceof ReferenceField) {
                $current_module = $current_field->getReferenceModuleByPrefix($path_tuple[1]);
            } else {
                throw new RuntimeException(
                    "Invalid field-type given within field-path. Only Reference- and AggregateFields are supported."
                );
            }
        }

        return $current_module->getField($destination_field);
    }
}
