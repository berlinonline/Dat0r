<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;
use Dat0r\Common\Error\RuntimeException;

class AttributeValuePath
{
    const PATH_DELIMITER = '.';

    public static function getAttributeValueByPath($document, $value_path)
    {
        // prepare path tuples
        $split_path = self::splitPath($value_path);
        $path_tuples = $split_path['path_tuples'];
        $target_attribute = $split_path['target_attribute'];
        $current_type = $document->getType();
        $current_document = $document;
        // loop into path
        foreach ($path_tuples as $path_tuple) {
            $offset_spec = self::parseOffsetExpression($path_tuple[1]);
            $current_attribute = $current_type->getAttribute($path_tuple[0]);
            $document_collection = $current_document->getValue($current_attribute->getName());
            // try to find the next document that matches the current offset_spec
            $type_offsets = array('_all' => 0);
            foreach ($document_collection as $next_document) {
                $type_prefix = $next_document->getType()->getPrefix();
                if (!isset($type_offsets[$type_prefix])) {
                    $type_offsets[$type_prefix] = 0;
                }
                if (self::documentMatchesOffsetSpec($next_document, $offset_spec, $type_offsets)) {
                    $current_document = $next_document;
                    break;
                }
                $type_offsets['_all']++;
                $type_offsets[$type_prefix]++;
            }
            // the value_path/offset_spec is valid, but doesn't match any documents in question
            if (!$current_document) {
                return null;
            }
            // prepare for next iteration by switching the current_type to the next level
            if ($current_attribute instanceof AggregateCollection) {
                $current_type = $current_attribute->getAggregateByPrefix($offset_spec['document_type']);
            } elseif ($current_attribute instanceof ReferenceCollection) {
                $current_type = $current_attribute->getReferenceByPrefix($offset_spec['document_type']);
            } else {
                throw new RuntimeException(
                    'Invalid attribute-type given within attribute-value-path.' .
                    'Only Reference- and AggregateCollections are supported.'
                );
            }
        }

        return $current_document->getValue($target_attribute);
    }

    protected static function splitPath($value_path)
    {
        $path_tuples = array();
        $path_parts = explode(self::PATH_DELIMITER, $value_path);

        if ($path_parts % 2 === 0) {
            throw new RuntimeException(
                'Invalid value-path(attribute_name) given.' .
                'Path parts must be made up of ' .
                '"{attribute_name}.{type_prefix}.{attribute_name}" parts with a single final attribute_name.'
            );
        }

        $next_tuple = array();
        for ($i = 1; $i <= count($path_parts); $i++) {
            $next_tuple[] = $path_parts[$i - 1];
            if ($i % 2 === 0) {
                $path_tuples[] = $next_tuple;
                $next_tuple = array();
            }
        }

        return array(
            'path_tuples' => $path_tuples,
            'target_attribute' => end($path_parts)
        );
    }

    protected static function parseOffsetExpression($offset_expression)
    {
        $compare_attribute = null;
        $compare_value = null;

        if (!preg_match('~(\w+|\*)(?:\[([\w="\-]+)\])~is', $offset_expression, $matches)) {
            throw new RuntimeException("Missing or invalid offset specification within attribute-value-path.");
        }

        $document_type = $matches[1];
        $collection_offset = $matches[2];

        if (
            preg_match('~(\w+)="([\w\-_]+)"~is', $collection_offset, $matches)
            && count($matches) === 3
        ) {
            $compare_attribute = $matches[1];
            $compare_value = $matches[2];
        } else {
            $collection_offset = (int)$collection_offset;
        }

        if ($compare_attribute) {
            return array(
                'type' => 'attribute',
                'document_type' => $document_type,
                'attribute_name' => $compare_attribute,
                'attribute_value' => $compare_value
            );
        } else {
            return array(
                'type' => 'index',
                'document_type' => $document_type,
                'position' => $collection_offset
            );
        }
    }

    protected static function documentMatchesOffsetSpec($document, $offset_spec, array $type_offsets)
    {
        $type_prefix = $document->getType()->getPrefix();
        $offset = $type_offsets['_all'];

        if (
            $offset_spec['document_type'] !== $type_prefix
            && $offset_spec['document_type'] !== '*'
        ) {
            return false;
        } elseif ($offset_spec['document_type'] === $type_prefix) {
            $offset = $type_offsets[$type_prefix];
        }

        if ($offset_spec['type'] === 'index') {
            return $offset === $offset_spec['position'];
        } else {
            return $document->getValue($offset_spec['attribute_name']) === $offset_spec['attribute_value'];
        }
    }
}
