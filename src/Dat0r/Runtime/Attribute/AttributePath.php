<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Runtime\IDocumentType;
use Dat0r\Runtime\Attribute\Bundle\AggregateCollection;
use Dat0r\Runtime\Attribute\Bundle\ReferenceCollection;
use Dat0r\Common\Error\RuntimeException;

class AttributePath
{
    const PATH_DELIMITER = '.';

    public static function getAttributePath(IAttribute $attribute)
    {
        $path_parts = array($attribute->getName());

        $current_attribute = $attribute->getParent();
        $current_type = $attribute->getType();
        while (
            $current_attribute instanceof AggregateCollection
            || $current_attribute instanceof ReferenceCollection
        ) {
            $path_parts[] = $current_type->getPrefix();
            $path_parts[] = $current_attribute->getName();

            $current_type = $current_attribute->getType();
            $current_attribute = $current_attribute->getParent();
        }

        return implode(self::PATH_DELIMITER, array_reverse($path_parts));
    }

    public static function getAttributeByPath(IDocumentType $type, $attribute_path)
    {
        $path_parts = explode(self::PATH_DELIMITER, $attribute_path);

        if ($path_parts % 2 === 0) {
            throw new RuntimeException(
                'Invalid attributepath(attribute_name) given.' .
                'Path parts must be made up of ' .
                '"{attribute_name}.{type_prefix}.{attribute_name}" parts with a single final attribute_name.'
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

        $destination_attribute = end($path_parts);
        $current_type = $type;

        foreach ($path_tuples as $path_tuple) {
            $current_attribute = $current_type->getAttribute($path_tuple[0]);
            if ($current_attribute instanceof AggregateCollection) {
                $current_type = $current_attribute->getAggregateByPrefix($path_tuple[1]);
            } elseif ($current_attribute instanceof ReferenceCollection) {
                $current_type = $current_attribute->getReferenceByPrefix($path_tuple[1]);
            } else {
                throw new RuntimeException(
                    'Invalid attribute-type given within attribute-path.' .
                    'Only Reference- and AggregateCollections are supported.'
                );
            }
        }

        return $current_type->getAttribute($destination_attribute);
    }
}