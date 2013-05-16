<?php

namespace Dat0r\Core\Runtime\Sham;

use Faker\Factory;

use Dat0r\Core\Runtime\Document\IDocument;
use Dat0r\Core\Runtime\Module\IModule;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field;

use Dat0r\Core\Runtime\Sham\Guesser\TextField AS TextFieldGuesser;

/**
 * Sham\DataGenerator is a class that is able to create or fill documents
 * containing fake data.
 *
 * @author Steffen Gransow <graste@mivesto.de>
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 */
class DataGenerator
{
    /**
     * name of options array key to use for an array of fieldname => value pairs
     */
    const OPTION_FIELD_VALUES = 'field_values';

    /**
     * name of options array key to use to exclude certain fields from fake data generation
     */
    const OPTION_EXCLUDED_FIELDS = 'excluded_fields';

    /**
     * name of options array key to use to mark changed documents as clean
     */
    const OPTION_MARK_CLEAN = 'mark_clean';

    /**
     * name of options array key to use to set the locale used for fake data generation
     */
    const OPTION_LOCALE = 'locale';

    /**
     * name of options array key to use to set the number of documents to generate
     */
    const OPTION_COUNT = 'count';

    /**
     * name of options array key to use to disable the guessing of fake data provider by fieldname
     */
    const OPTION_GUESS_PROVIDER_BY_NAME = 'guess_provider_by_name';

    /**
     * name of options array key to use to set the current level of recursion (for reference fields)
     */
    const OPTION_RECURSION_LEVEL = 'recursion_level';

    /**
     * This method fills the document with fake data. You may customize the
     * fake data used for each field by using the options array.
     *
     * Supported options:
     * - OPTION_LOCALE: Locale for fake data (e.g. 'en_UK', defaults to 'de_DE').
     * - OPTION_MARK_CLEAN: Calls `$document->markClean()` at the end to prevent
     *                 change events to occur after faking data. Default is false.
     * - OPTION_FIELD_VALUES: array of `fieldname` => `value` pairs to customize
     *                  fake values per field of the given document. You can
     *                  either specify a direct value or provide a closure. The
     *                  closure must return the value you want to set on that field.
     * - OPTION_EXCLUDED_FIELDS: Array of fieldnames to excluded from filling
     *                  with fake data.
     * - OPTION_GUESS_PROVIDER_BY_NAME: Boolean true by default. Certain fieldnames
     *                  trigger different providers (e.g. firstname or email).
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     *
     * @throws \Dat0r\Core\Runtime\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Runtime\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Runtime\Error\InvalidImplementorException on AggregateField misconfiguration
     */
    public static function fill(IDocument $document, array $options = array())
    {
        $locale = 'de_DE';
        if (!empty($options[self::OPTION_LOCALE]))
        {
            $loc = $options[self::OPTION_LOCALE];
            if (!is_string($loc) || !preg_match('#[a-z]{2,6}_[A-Z]{2,6}#', $loc))
            {
                throw new \InvalidArgumentException('Given option "' . self::OPTION_LOCALE . '" is not a valid string. Use "languageCode_countryCode", e.g. "de_DE" or "en_UK".');
            }
            $locale = $loc;
        }

        $fields_to_exclude = array();
        if (!empty($options[self::OPTION_EXCLUDED_FIELDS]))
        {
            $excluded = $options[self::OPTION_EXCLUDED_FIELDS];
            if (!is_array($excluded))
            {
                throw new \InvalidArgumentException('Given option "' . self::OPTION_EXCLUDED_FIELDS . '" is not an array. It should be an array of fieldnames.');
            }
            $fields_to_exclude = $excluded;
        }

        $faker = Factory::create($locale);

        $module = $document->getModule();
        foreach ($module->getFields() as $fieldname => $field)
        {
            if (in_array($fieldname, $fields_to_exclude, TRUE))
            {
                continue;
            }

            $name = self::getMethodNameFor($field);
            if (NULL !== $name && is_callable("\\Dat0r\\Core\\Runtime\\Sham\\DataGenerator::$name"))
            {
                self::$name($faker, $document, $field, $options);
            }
            else
            {
                self::setValue($document, $field, $field->getDefaultValue(), $options);
            }
        }

        if (array_key_exists(self::OPTION_MARK_CLEAN, $options) && TRUE === $options[self::OPTION_MARK_CLEAN])
        {
            $document->markClean();
        }
    }

    /**
     * Creates an array with fake data for the given module.
     *
     * @param IModule $module module to create fake data for
     * @param array $options For valid options see fill() method
     *
     * @return array of fake data for the given module
     *
     * @throws \InvalidArgumentException in case of invalid option string (e.g. count or excluded fields or locale)
     * @throws \Dat0r\Core\Runtime\Document\InvalidValueException in case of fake data being invalid for the given field
     */
    public static function createDataFor(IModule $module, array $options = array())
    {
        $document = $module->createDocument();
        self::fill($document, $options);
        return $document->toArray();
    }

    /**
     * Creates a document with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options For valid options see fill() method
     *
     * @return document newly created with fake data
     *
     * @throws \InvalidArgumentException in case of invalid option string (e.g. count or excluded fields or locale)
     * @throws \Dat0r\Core\Runtime\Document\InvalidValueException in case of fake data being invalid for the given field
     */
    public static function createDocument(IModule $module, array $options = array())
    {
        $options[self::OPTION_MARK_CLEAN] = TRUE;
        $document = $module->createDocument();
        self::fill($document, $options);
        return $document;
    }

    /**
     * Creates `count` number of documents with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options use `count` for number of documents to create. For other options see fill() method.
     *
     * @return array of new documents with fake data
     *
     * @throws \InvalidArgumentException in case of invalid option string (e.g. count or excluded fields or locale)
     * @throws \Dat0r\Core\Runtime\Document\InvalidValueException in case of fake data being invalid for the given field
     */
    public static function createDocuments(IModule $module, array $options = array())
    {
        $documents = array();

        $count = 10;
        if (!empty($options[self::OPTION_COUNT]))
        {
            $cnt = $options[self::OPTION_COUNT];
            if (!is_int($cnt))
            {
                throw new \InvalidArgumentException('Given option "' . self::OPTION_COUNT . '" is not an integer. Provide a correct value or use fallback to default count.');
            }
            $count = $cnt;
            unset($options[self::OPTION_COUNT]);
        }

        for ($i = 0; $i < $count; $i++) {
             $documents[] = self::createDocument($module, $options);
        }

        return $documents;
    }

    protected static function addText($faker, IDocument $document, IField $field, $options)
    {
        $value = $faker->words($faker->randomNumber(1, 3), TRUE);
        if (self::shouldGuessByName($options))
        {
            $closure = TextFieldGuesser::guess($field->getName(), $faker);
            if (!empty($closure) && is_callable($closure))
            {
                $value = $closure();
            }
        }
        self::setValue($document, $field, $value, $options);
    }

    protected static function addTextCollection($faker, IDocument $document, IField $field, $options)
    {
        $values = array();

        $numberOfValues = $faker->randomNumber(1, 5);
        for ($i = 0; $i < $numberOfValues; $i++)
        {
            $text = $faker->words($faker->randomNumber(1, 3), TRUE);
            if (self::shouldGuessByName($options))
            {
                $closure = TextFieldGuesser::guess($field->getName(), $faker);
                if (!empty($closure) && is_callable($closure))
                {
                    $text = $closure();
                }
            }
            $values[] = $text;
        }

        self::setValue($document, $field, $values, $options);
    }

    protected static function addTextarea($faker, IDocument $document, IField $field, $options)
    {
        $text = $faker->paragraphs($faker->randomNumber(1, 5));
        self::setValue($document, $field, implode(PHP_EOL . PHP_EOL, $text), $options);
    }

    protected static function addInteger($faker, IDocument $document, IField $field, $options)
    {
        self::setValue($document, $field, $faker->numberBetween(1, 99999), $options);
    }

    protected static function addIntegerCollection($faker, IDocument $document, IField $field, $options)
    {
        $values = array();

        $numberOfValues = $faker->randomNumber(1, 5);
        for ($i = 0; $i < $numberOfValues; $i++)
        {
            $values[] = $faker->numberBetween(1, 99999);
        }

        self::setValue($document, $field, $values, $options);
    }

    protected static function addKeyValue($faker, IDocument $document, IField $field, $options)
    {
        $values = array();

        $numberOfValues = $faker->randomNumber(1, 5);
        for ($i = 0; $i < $numberOfValues; $i++)
        {
            $values[$faker->word] = $faker->sentence;
        }

        self::setValue($document, $field, $values, $options);
    }

    protected static function addKeyValuesCollection($faker, IDocument $document, IField $field, $options)
    {
        $collection = array();

        $numberOfEntries = $faker->numberBetween(1, 5);
        for ($i = 0; $i < $numberOfEntries; $i++)
        {
            $values = array();
            $numberOfValues = $faker->numberBetween(1, 5);
            for ($i = 0; $i < $numberOfValues; $i++)
            {
                $values[] = $faker->words($faker->numberBetween(1, 3), TRUE);
            }
            $collection[$faker->word] = $values;
        }

        self::setValue($document, $field, $collection, $options);
    }

    protected static function addBoolean($faker, IDocument $document, IField $field, $options)
    {
        self::setValue($document, $field, $faker->boolean, $options);
    }

    protected static function addAggregateModule($faker, IDocument $document, IField $field, $options)
    {
        $recursion_level = 1;
        if (!empty($options[self::OPTION_RECURSION_LEVEL]) && is_int($options[self::OPTION_RECURSION_LEVEL]))
        {
            $recursion_level = $options[self::OPTION_RECURSION_LEVEL];
        }
        else
        {
            $options[self::OPTION_RECURSION_LEVEL] = $recursion_level;
        }

        if ($recursion_level > 1)
        {
            return;
        }

        $options_clone = $options;
        $options_clone[self::OPTION_RECURSION_LEVEL] = $recursion_level + 1;

        $aggregateModule = $field->getAggregateModule();
        $data = self::createDataFor($aggregateModule, $options_clone);

        self::setValue($document, $field, $data, $options);
    }

    protected static function addReference($faker, IDocument $document, IField $field, $options)
    {
        $recursion_level = 1;
        if (!empty($options[self::OPTION_RECURSION_LEVEL]) && is_int($options[self::OPTION_RECURSION_LEVEL]))
        {
            $recursion_level = $options[self::OPTION_RECURSION_LEVEL];
        }
        else
        {
            $options[self::OPTION_RECURSION_LEVEL] = $recursion_level;
        }

        if ($recursion_level > 1)
        {
            return;
        }

        $options_clone = $options;
        $options_clone[self::OPTION_RECURSION_LEVEL] = $recursion_level + 1;

        $referencedModules = $field->getReferencedModules();
        $collection = $field->getDefaultValue();

        $numberOfReferencedModules = count($referencedModules);
        $numberOfNewReferenceEntries = $faker->numberBetween(1, 3);

        // add number of documents to reference depending on number of referenced modules
        for ($i = 0; $i < $numberOfReferencedModules; $i++)
        {
            $numberOfNewReferenceEntries += $faker->numberBetween(0, 3);
        }

        // add new documents to collection for referenced modules
        for ($i = 0; $i < $numberOfNewReferenceEntries; $i++)
        {
            $ref_module = $faker->randomElement($referencedModules);
            $new_document = self::createDocument($ref_module, $options_clone);
            $collection->add($new_document);
        }

        self::setValue($document, $field, $collection, $options);
    }

    /**
     * Sets either given default value or value from option to the given field.
     *
     * @param Document $document the document to modify
     * @param string $fieldname the name of the field to set a value for
     * @param mixed $default_value Default value to set.
     * @param array $options Array containing a `fieldname => $mixed` entry.
     *                       $mixed is set as value instead of $default_value.
     *                       If $mixed is a closure it will be called and used.
     *
     * @return void
     */
    protected static function setValue(IDocument $document, IField $field, $default_value, $options)
    {
        $fieldname = $field->getName();
        $fieldoptions = array();
        if (!empty($options[self::OPTION_FIELD_VALUES]) && is_array($options[self::OPTION_FIELD_VALUES]))
        {
            $fieldoptions = $options[self::OPTION_FIELD_VALUES];
        }

        if (empty($fieldoptions[$fieldname]))
        {
            $document->setValue($fieldname, $default_value);
        }
        else
        {
            $option = $fieldoptions[$fieldname];
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

    protected static function shouldGuessByName($options)
    {
        if (array_key_exists(self::OPTION_GUESS_PROVIDER_BY_NAME, $options) && FALSE === $options[self::OPTION_GUESS_PROVIDER_BY_NAME])
        {
            return FALSE;
        }
        return TRUE;
    }

    protected static function getMethodNameFor($field)
    {
        $type = get_class($field);
        $name = NULL;
        if (preg_match('/^Dat0r\\\\Core\\\\Runtime\\\\Field\\\\(.*)Field$/', $type, $matches))
        {
            $name = 'add' . $matches[1];
        }

        return $name;
    }
}
