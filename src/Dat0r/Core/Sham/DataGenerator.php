<?php

namespace Dat0r\Core\Sham;

use Faker\Factory;

use Dat0r\Core\Document\IDocument;
use Dat0r\Core\Module\IModule;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field;
use Dat0r\Core\Sham\Guesser\TextField as TextFieldGuesser;
use Dat0r\Core\Document\DocumentCollection;

/**
 * Sham\DataGenerator is a class that is able to create or fill documents
 * containing fake data.
 *
 * @author Steffen Gransow <graste@mivesto.de>
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 */
class DataGenerator
{
    protected $faker;

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

    public function __construct($locale = 'de_DE')
    {
        $this->locale = $locale;
        $this->faker = Factory::create($this->locale);
    }

    /**
     * This method fills the given document with fake data. You may customize
     * the fake data generation used for each field by using the options array.
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
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public function fake(IDocument $document, array $options = array())
    {
        if (!empty($options[self::OPTION_LOCALE])) {
            $loc = $options[self::OPTION_LOCALE];
            if (!is_string($loc) || !preg_match('#[a-z]{2,6}_[A-Z]{2,6}#', $loc)) {
                throw new \InvalidArgumentException(
                    'Given option "' . self::OPTION_LOCALE
                    . '" is not a valid string. Use "languageCode_countryCode", e.g. "de_DE" or "en_UK".'
                );
            }
            $this->locale = $loc;
            $this->faker = Factory::create($this->locale);
        }

        $fields_to_exclude = array();
        if (!empty($options[self::OPTION_EXCLUDED_FIELDS])) {
            $excluded = $options[self::OPTION_EXCLUDED_FIELDS];
            if (!is_array($excluded)) {
                throw new \InvalidArgumentException(
                    'Given option "' . self::OPTION_EXCLUDED_FIELDS
                    . '" is not an array. It should be an array of fieldnames.'
                );
            }
            $fields_to_exclude = $excluded;
        }

        $module = $document->getModule();
        foreach ($module->getFields() as $fieldname => $field) {
            if (in_array($fieldname, $fields_to_exclude, true)) {
                continue;
            }

            $name = $this->getMethodNameFor($field);
            if (null !== $name && is_callable(array($this, $name))) {
                $this->$name($document, $field, $options);
            } else {
                $this->setValue($document, $field, $field->getDefaultValue(), $options);
            }
        }

        if (array_key_exists(self::OPTION_MARK_CLEAN, $options)
            && true === $options[self::OPTION_MARK_CLEAN]
        ) {
            $document->markClean();
        }
    }

    /**
     * Creates an array with fake data for the given module.
     *
     * @param IModule $module module to create fake data for
     * @param array $options For valid options see fake() method
     *
     * @return array of fake data for the given module
     *
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public function fakeData(IModule $module, array $options = array())
    {
        $document = $module->createDocument();
        $this->fake($document, $options);
        return $document->toArray();
    }

    /**
     * Creates a document with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options For valid options see fake() method
     *
     * @return document newly created with fake data
     *
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public function createFakeDocument(IModule $module, array $options = array())
    {
        $options[self::OPTION_MARK_CLEAN] = true;
        $document = $module->createDocument();
        $this->fake($document, $options);
        return $document;
    }

    /**
     * Creates `count` number of documents with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options use `count` for number of documents to create. For other options see fake() method.
     *
     * @return array of new documents with fake data
     *
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public function createFakeDocuments(IModule $module, array $options = array())
    {
        $documents = array();

        $count = 10;
        if (!empty($options[self::OPTION_COUNT])) {
            $cnt = $options[self::OPTION_COUNT];
            if (!is_int($cnt)) {
                throw new \InvalidArgumentException(
                    'Given option "' . self::OPTION_COUNT
                    . '" is not an integer. Provide a correct value or use fallback to default count.'
                );
            }
            $count = $cnt;
            unset($options[self::OPTION_COUNT]);
        }

        for ($i = 0; $i < $count; $i++) {
             $documents[] = $this->createFakeDocument($module, $options);
        }

        return $documents;
    }

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
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public static function fill(IDocument $document, array $options = array())
    {
        $data_generator = new static();
        $data_generator->fake($document, $options);
    }

    /**
     * Creates an array with fake data for the given module.
     *
     * @param IModule $module module to create fake data for
     * @param array $options For valid options see fill() method
     *
     * @return array of fake data for the given module
     *
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public static function createDataFor(IModule $module, array $options = array())
    {
        $data_generator = new static();
        return $data_generator->fakeData($module, $options);
    }

    /**
     * Creates a document with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options For valid options see fill() method
     *
     * @return document newly created with fake data
     *
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public static function createDocument(IModule $module, array $options = array())
    {
        $data_generator = new static();
        return $data_generator->createFakeDocument($module, $options);
    }

    /**
     * Creates `count` number of documents with fake data for the given module.
     *
     * @param IModule $module module to create documents for
     * @param array $options use `count` for number of documents to create. For other options see fill() method.
     *
     * @return array of new documents with fake data
     *
     * @throws \Dat0r\Core\Document\InvalidValueException in case of fake data being invalid for the given field
     * @throws \InvalidArgumentException in case of invalid locale option string
     * @throws \Dat0r\Core\Error\LogicException on AggregateField misconfiguration
     * @throws \Dat0r\Core\Error\InvalidImplementorException on various misconfigurations
     * @throws \Dat0r\Core\Error\ObjectImmutableException if an instance is frozen (closed to modifications)
     */
    public static function createDocuments(IModule $module, array $options = array())
    {
        $data_generator = new DataGenerator();
        return $data_generator->createFakeDocuments($module, $options);
    }

    /**
     * Generates and adds fake data for a TextField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the TextField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addText(IDocument $document, IField $field, array $options = array())
    {
        $value = $this->faker->words($this->faker->numberBetween(1, 3), true);

        if ($this->shouldGuessByName($options)) {
            $closure = TextFieldGuesser::guess($field->getName(), $this->faker);
            if (!empty($closure) && is_callable($closure)) {
                $value = call_user_func($closure);
            }
        }

        $this->setValue($document, $field, $value, $options);
    }

    /**
     * Generates and adds fake data for a TextCollectionField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the TextCollectionField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addTextCollection(IDocument $document, IField $field, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $text = $this->faker->words($this->faker->numberBetween(1, 3), true);
            if ($this->shouldGuessByName($options)) {
                $closure = TextFieldGuesser::guess($field->getName(), $this->faker);
                if (!empty($closure) && is_callable($closure)) {
                    $text = call_user_func($closure);
                }
            }
            $values[] = $text;
        }

        $this->setValue($document, $field, $values, $options);
    }

    /**
     * Generates and adds fake data for a TextareaField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the TextareaField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addTextarea(IDocument $document, IField $field, array $options = array())
    {
        $text = $this->faker->paragraphs($this->faker->numberBetween(1, 5));
        $this->setValue($document, $field, implode(PHP_EOL . PHP_EOL, $text), $options);
    }

    /**
     * Generates and adds fake data for an IntegerField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the IntegerField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addInteger(IDocument $document, IField $field, array $options = array())
    {
        $this->setValue($document, $field, $this->faker->numberBetween(1, 99999), $options);
    }

    /**
     * Generates and adds fake data for an IntegerCollectionField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the IntegerCollectionField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addIntegerCollection(IDocument $document, IField $field, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $values[] = $this->faker->numberBetween(1, 99999);
        }

        $this->setValue($document, $field, $values, $options);
    }

    /**
     * Generates and adds fake data for a KeyValueField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the KeyValueField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addKeyValue(IDocument $document, IField $field, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $values[$this->faker->word] = $this->faker->sentence;
        }

        $this->setValue($document, $field, $values, $options);
    }

    /**
     * Generates and adds fake data for a KeyValuesCollectionField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the KeyValuesCollectionField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addKeyValuesCollection(IDocument $document, IField $field, array $options = array())
    {
        $collection = array();

        $numberOfEntries = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $numberOfEntries; $i++) {
            $values = array();
            $number_of_values = $this->faker->numberBetween(1, 5);
            for ($i = 0; $i < $number_of_values; $i++) {
                $values[] = $this->faker->words($this->faker->numberBetween(1, 3), true);
            }
            $collection[$this->faker->word] = $values;
        }

        $this->setValue($document, $field, $collection, $options);
    }

    /**
     * Generates and adds fake data for a BooleanField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the BooleanField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addBoolean(IDocument $document, IField $field, array $options = array())
    {
        $this->setValue($document, $field, $this->faker->boolean, $options);
    }

    /**
     * Generates and adds fake data for an AggregateModuleField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the AggregateModuleField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addAggregate(IDocument $document, IField $field, array $options = array())
    {
        $options_clone = $options;
        $document_collection = new DocumentCollection();
        $aggregate_modules = $field->getAggregateModules();

        $number_of_aggregate_modules = count($aggregate_modules);
        $number_of_new_aggregate_entries = $this->faker->numberBetween(1, 3);

        // add number of documents to reference depending on number of aggregate modules
        for ($i = 0; $i < $number_of_aggregate_modules; $i++) {
            $number_of_new_aggregate_entries += $this->faker->numberBetween(0, 3);
        }

        // add new documents to collection for aggregate modules
        for ($i = 0; $i < $number_of_new_aggregate_entries; $i++) {
            $aggregate_module = $this->faker->randomElement($aggregate_modules);
            $new_document = $this->createFakeDocument($aggregate_module, $options_clone);
            $document_collection->add($new_document);
        }

        $this->setValue($document, $field, $document_collection, $options);
    }

    /**
     * Generates and adds fake data for a ReferenceField on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IField $field an instance of the ReferenceField to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addReference(IDocument $document, IField $field, array $options = array())
    {
        $recursion_level = 1;
        if (!empty($options[self::OPTION_RECURSION_LEVEL])
            && is_int($options[self::OPTION_RECURSION_LEVEL])
        ) {
            $recursion_level = $options[self::OPTION_RECURSION_LEVEL];
        }

        if ($recursion_level > 1) {
            return;
        }

        $options_clone = $options;
        $options_clone[self::OPTION_RECURSION_LEVEL] = $recursion_level + 1;

        $referencedModules = $field->getReferencedModules();
        $collection = $field->getDefaultValue();

        $numberOfReferencedModules = count($referencedModules);
        $numberOfNewReferenceEntries = $this->faker->numberBetween(1, 3);

        // add number of documents to reference depending on number of referenced modules
        for ($i = 0; $i < $numberOfReferencedModules; $i++) {
            $numberOfNewReferenceEntries += $this->faker->numberBetween(0, 3);
        }

        // add new documents to collection for referenced modules
        for ($i = 0; $i < $numberOfNewReferenceEntries; $i++) {
            $ref_module = $this->faker->randomElement($referencedModules);
            $new_document = $this->createFakeDocument($ref_module, $options_clone);
            $collection->add($new_document);
        }

        $this->setValue($document, $field, $collection, $options);
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
     *                       $mixed may also be another callable like an array
     *                       `array($class, "$methodName")` or a string like
     *                       `'Your\Namespace\Foo::getStaticTrololo'`.
     *
     * @return void
     */
    protected function setValue(
        IDocument $document,
        IField $field,
        $default_value,
        array $options = array()
    ) {
        $fieldname = $field->getName();
        $fieldoptions = array();

        if (!empty($options[self::OPTION_FIELD_VALUES])
            && is_array($options[self::OPTION_FIELD_VALUES])
        ) {
            $fieldoptions = $options[self::OPTION_FIELD_VALUES];
        }

        if (empty($fieldoptions[$fieldname])) {
            $document->setValue($fieldname, $default_value);
        } else {
            $option = $fieldoptions[$fieldname];
            if (is_callable($option)) {
                $document->setValue($fieldname, call_user_func($option));
            } else {
                $document->setValue($fieldname, $option);
            }
        }
    }

    /**
     * Returns whether or not the fake data generation should be dependant on
     * the fieldnames the used modules have.
     *
     * @param array $options array of options to customize fake data creation.
     *
     * @return bool true if the fake data provider should be guessed by fieldname.
     *                   False if specified self::OPTION_GUESS_PROVIDER_BY_NAME is set to false.
     */
    protected function shouldGuessByName(array $options = array())
    {
        if (array_key_exists(self::OPTION_GUESS_PROVIDER_BY_NAME, $options)
            && false === $options[self::OPTION_GUESS_PROVIDER_BY_NAME]
        ) {
            return false;
        }
        return true;
    }

    /**
     * Returns the name of the internal method to call when fake data should
     * be generated and added to the given field. The pattern is like this:
     *
     * - `addText` for `\Dat0r\Core\Field\TextField`
     * - `addIntegerCollection` for `\Dat0r\Core\Field\IntegerCollectionField`
     * - `addReferenceModule` for `\Dat0r\Core\Field\ReferenceModuleField`
     *
     * etc. pp.
     *
     * @param IField $field field instance to generate fake data for
     *
     * @return string method name to use for fake data addition for given field
     */
    protected function getMethodNameFor(IField $field)
    {
        $name = null;

        $type = get_class($field);
        if (preg_match('/^Dat0r\\\\Core\\\\Field\\\\(.*)Field$/', $type, $matches)) {
            $name = 'add' . $matches[1];
        }

        return $name;
    }
}
