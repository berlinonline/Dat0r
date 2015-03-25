<?php

namespace Dat0r\Runtime\Attribute\Image;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Rule\Type\FloatRule;
use Dat0r\Runtime\Validator\Rule\Type\IntegerRule;
use Dat0r\Runtime\Validator\Rule\Type\KeyValueListRule;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;
use Dat0r\Runtime\Validator\Rule\Type\UrlRule;
use Exception;

class ImageRule extends Rule
{
    const OPTION_ALLOWED_KEYS               = 'allowed_keys';
    const OPTION_ALLOWED_VALUES             = 'allowed_values';
    const OPTION_ALLOWED_PAIRS              = 'allowed_pairs';

    /**
     * Option to define that meta_data values must be of a certain scalar type.
     */
    const OPTION_VALUE_TYPE                 = 'value_type';

    const VALUE_TYPE_BOOLEAN                = 'boolean';
    const VALUE_TYPE_INTEGER                = 'integer';
    const VALUE_TYPE_FLOAT                  = 'float';
    const VALUE_TYPE_SCALAR                 = 'scalar'; // any of integer, float, boolean or string
    const VALUE_TYPE_TEXT                   = 'text';

    const OPTION_MAX_VALUE                  = 'max_value'; // when value_type is float or int
    const OPTION_MIN_VALUE                  = 'min_value'; // when value_type is float or int

    // text rule options
    const OPTION_ALLOW_CRLF                 = TextRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = TextRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = TextRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = TextRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = TextRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = TextRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = TextRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = TextRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = TextRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = TextRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = TextRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = TextRule::OPTION_TRIM;

    // integer rule options
    const OPTION_ALLOW_HEX                  = IntegerRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL                = IntegerRule::OPTION_ALLOW_OCTAL;
    const OPTION_MAX_INTEGER_VALUE          = 'max_integer_value'; // IntegerRule::OPTION_MAX_VALUE;
    const OPTION_MIN_INTEGER_VALUE          = 'min_integer_value'; // IntegerRule::OPTION_MIN_VALUE;

    // float rule options
    const OPTION_ALLOW_THOUSAND_SEPARATOR   = FloatRule::OPTION_ALLOW_THOUSAND_SEPARATOR;
    const OPTION_PRECISION_DIGITS           = FloatRule::OPTION_PRECISION_DIGITS;
    const OPTION_ALLOW_INFINITY             = FloatRule::OPTION_ALLOW_INFINITY;
    const OPTION_ALLOW_NAN                  = FloatRule::OPTION_ALLOW_NAN;
    const OPTION_MAX_FLOAT_VALUE            = 'max_float_value'; // FloatRule::OPTION_MAX_VALUE;
    const OPTION_MIN_FLOAT_VALUE            = 'min_float_value'; // FloatRule::OPTION_MIN_VALUE;

    protected function execute($value)
    {
        try {
            if (is_array($value)) {
                if (!empty($value) && !$this->isAssoc($value)) {
                    $this->throwError('non_assoc_array', [ 'value' => $value ], IncidentInterface::CRITICAL);
                    return false;
                }
                $image = Image::createFromArray($value);
            } elseif ($value instanceof Image) {
                $image = Image::createFromImage($value);
            } else {
                $this->throwError('invalid_type', [ 'value' => $value ], IncidentInterface::CRITICAL);
                return false;
            }

            $text_rule = new TextRule('valid-text', $this->getOptions());

            $storage_location = '';
            if (!$text_rule->apply($image->getStorageLocation())) {
                $this->throwIncidentsAsErrors($text_rule);
                return false;
            }
            $storage_location = $text_rule->getSanitizedValue();

            $title = '';
            if (!$text_rule->apply($image->getTitle())) {
                $this->throwIncidentsAsErrors($text_rule);
                return false;
            }
            $title = $text_rule->getSanitizedValue();

            $caption = '';
            if (!$text_rule->apply($image->getCaption())) {
                $this->throwIncidentsAsErrors($text_rule);
                return false;
            }
            $caption = $text_rule->getSanitizedValue();

            $copyright = '';
            if (!$text_rule->apply($image->getCopyright())) {
                $this->throwIncidentsAsErrors($text_rule);
                return false;
            }
            $copyright = $text_rule->getSanitizedValue();

            $url_rule = new UrlRule('valid-url', $this->getOptions());
            $copyright_url = '';
            if (!$url_rule->apply($image->getCopyrightUrl())) {
                $this->throwIncidentsAsErrors($url_rule);
                return false;
            }
            $copyright_url = $url_rule->getSanitizedValue();

            $source = '';
            if (!$text_rule->apply($image->getSource())) {
                $this->throwIncidentsAsErrors($text_rule);
                return false;
            }
            $source = $text_rule->getSanitizedValue();

            // meta data accepts scalar values
            $meta_data = [];
            $key_value_list_rule = new KeyValueListRule('valid-key-value-list', $this->getOptions());
            if (!$key_value_list_rule->apply($image->getMetaData())) {
                $this->throwIncidentsAsErrors($key_value_list_rule);
                return false;
            }
            $meta_data = $key_value_list_rule->getSanitizedValue();

            $this->setSanitizedValue(
                new Image(
                    $storage_location,
                    $title,
                    $caption,
                    $copyright,
                    $copyright_url,
                    $source,
                    $meta_data
                )
            );
        } catch (Exception $e) {
            $this->throwError(
                'invalid_image_data',
                [
                    'error' => $e->getMessage()
                ],
                IncidentInterface::CRITICAL
            );
            return false;
        }

        return true;
    }

    /**
     * @return bool true if argument is an associative array. False otherwise.
     */
    protected function isAssoc(array $array)
    {
        foreach (array_keys($array) as $key => $value) {
            if ($key !== $value) {
                return true;
            }
        }

        return false;
    }
}
