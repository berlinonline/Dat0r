<?php

namespace Dat0r\Core\Runtime\Sham;

use Dat0r\Core\Runtime\Module\IModule;
use Dat0r\Core\Runtime\Document\IDocument;
use Dat0r\Core\Runtime\Field;

/**
 * Sham\DataGenerator is a class that is able to create documents containing
 * fake data. It's also possible to fill existing documents with fake data.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Steffen Gransow <graste@mivesto.de>
 */
class DataGenerator
{
    /**
     * This method fills the document with fake data. You may customize the
     * fake data used for each field by using the options array.
     *
     * Supported options:
     * - `locale`: Locale for fake data (e.g. 'en_UK', defaults to 'de_DE').
     * - `mark_clean`: Calls `$document->markClean()` at the end to prevent
     *                 change events to occur after faking data. Default false.
     * - `fields`: array of `fieldname` => `value` pairs to customize fake
     *             values per field of the given document. You can either
     *             specify a direct value or provide a closure. The closure
     *             must return the value you want to set on that field.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     *
     * @throws \InvalidArgumentException in case of invalid locale option string
     */
    public static function fill(IDocument $document, array $options = array())
    {
        $locale = 'de_DE';
        if (!empty($options['locale']))
        {
            $loc = $options['locale'];
            if (!is_string($loc) || !preg_match('#[a-z]{2,6}_[A-Z]{2,6}#', $loc))
            {
                throw new \InvalidArgumentException('Given locale is not a valid string. Use "languageCode_countryCode", e.g. "de_DE" or "en_UK".');
            }
            $locale = $loc;
        }

        $faker = \Faker\Factory::create($locale);

        $fieldoptions = array();
        if (!empty($options['fields']) && is_array($options['fields']))
        {
            $fieldoptions = $options['fields'];
        }

        $module = $document->getModule();
        foreach ($module->getFields() as $fieldname => $field)
        {
            $type = get_class($field);
            $value = NULL;

            switch ($type)
            {
                case 'Dat0r\Core\Runtime\Field\TextField':
                {
                    self::addValue($document, $fieldname, $faker->words(2, true), $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\TextareaField':
                {
                    self::addValue($document, $fieldname, $faker->paragraphs(4, true), $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\IntegerField':
                {
                    self::addValue($document, $fieldname, $faker->numberBetween(1, 99999), $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\IntegerCollectionField':
                {
                    $values = array();
                    for ($i = 0; $i < 5; $i++)
                    {
                        $values[] = $faker->numberBetween(1, 99999);
                    }
                    self::addValue($document, $fieldname, $values, $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\KeyValueField':
                {
                    self::addValue($document, $fieldname, array($faker->word => $faker->sentence), $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\KeyValuesCollectionField':
                {
                    $values = array();
                    for ($i = 0; $i < 5; $i++)
                    {
                        $values[] = array($faker->word => $faker->sentence);
                    }
                    self::addValue($document, $fieldname, $values, $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\BooleanField':
                {
                    self::addValue($document, $fieldname, $faker->boolean, $fieldoptions);
                    break;
                }
                case 'Dat0r\Core\Runtime\Field\UuidField':
                {
                    self::addValue($document, $fieldname, $faker->uuid, $fieldoptions);
                    break;
                }
                default:
                {
                    self::addValue($document, $fieldname, $field->getDefaultValue(), $fieldoptions);
                    break;
                }
            }
        }

        if (array_key_exists('mark_clean', $options) && TRUE === $options['mark_clean'])
        {
            $document->markClean();
        }
    }

    /**
     * Creates a document with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options For valid options see @see fill method
     *
     * @return newly created document with fake data
     *
     * @throws \InvalidArgumentException in case of invalid integer value for `count` option
     */
    public static function createDocument(IModule $module, array $options = array())
    {
        $options['mark_clean'] = true;
        $document = $module->createDocument();
        self::fill($document, $options);
        return $document;
    }

    /**
     * Creates `count` number of documents for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options use `count` for number of documents to create. For other options see @see fill method
     *
     * @return array of new documents with fake data
     *
     * @throws \InvalidArgumentException in case of invalid integer value for `count` option
     */
    public static function createDocuments(IModule $module, array $options = array())
    {
        $documents = array();

        $count = 10;
        if (!empty($options['count']))
        {
            $cnt = $options['count'];
            if (!is_int($cnt))
            {
                throw new \InvalidArgumentException('Given count is not an integer. Provide a correct value or use fallback to default count.');
            }
            $count = $cnt;
            unset($options['count']);
        }

        for ($i = 0; $i < $count; $i++) {
             $documents[] = self::createDocument($module, $options);
        }

        return $documents;
    }

    /**
     * Sets either given default value or value from option to the given field.
     *
     * @param Document $document the document to modify
     * @param string $fieldname the name of the field to set a value for
     * @param mixed $default_value Default value to set.
     * @param array $options array Array containing a `fieldname => $mixed` entry.
     *                       $mixed is set as value instead of $default_value.
     *                       If $mixed is a closure it will be called and used.
     *
     * @return void
     */
    protected static function addValue(IDocument $document, $fieldname, $default_value, $options)
    {
        if (empty($options[$fieldname]))
        {
            $document->setValue($fieldname, $default_value);
        }
        else
        {
            $option = $options[$fieldname];
            if (is_callable($option))
            {
                $document->setValue($fieldname, $option());
            }
            else
            {
                $document->setValue($fieldname, $option);
            }
        }
    }
}
