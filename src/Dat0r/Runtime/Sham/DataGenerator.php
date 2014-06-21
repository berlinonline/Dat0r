<?php

namespace Dat0r\Runtime\Sham;

use Faker\Factory;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Document\IDocument;
use Dat0r\Runtime\IDocumentType;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;
use Dat0r\Runtime\Sham\Guesser\Text as TextGuesser;
use Dat0r\Runtime\Document\DocumentList;

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

    protected $locale;

    /**
     * name of options array key to use for an array of attribute_name => value pairs
     */
    const OPTION_FIELD_VALUES = 'attribute_values';

    /**
     * name of options array key to use to exclude certain attributes from fake data generation
     */
    const OPTION_EXCLUDED_FIELDS = 'excluded_attributes';

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
     * name of options array key to use to disable the guessing of fake data provider by attribute_name
     */
    const OPTION_GUESS_PROVIDER_BY_NAME = 'guess_provider_by_name';

    /**
     * name of options array key to use to set the current level of recursion (for reference attributes)
     */
    const OPTION_RECURSION_LEVEL = 'recursion_level';

    public function __construct($locale = 'de_DE')
    {
        $this->locale = $locale;
        $this->faker = Factory::create($this->locale);
    }

    /**
     * This method fills the given document with fake data. You may customize
     * the fake data generation used for each attribute by using the options array.
     *
     * Supported options:
     * - OPTION_LOCALE: Locale for fake data (e.g. 'en_UK', defaults to 'de_DE').
     * - OPTION_MARK_CLEAN: Calls `$document->markClean()` at the end to prevent
     *                 change events to occur after faking data. Default is false.
     * - OPTION_FIELD_VALUES: array of `attribute_name` => `value` pairs to customize
     *                  fake values per attribute of the given document. You can
     *                  either specify a direct value or provide a closure. The
     *                  closure must return the value you want to set on that attribute.
     * - OPTION_EXCLUDED_FIELDS: Array of attribute_names to excluded from filling
     *                  with fake data.
     * - OPTION_GUESS_PROVIDER_BY_NAME: Boolean true by default. Certain attribute_names
     *                  trigger different providers (e.g. firstname or email).
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public function fake(IDocument $document, array $options = array())
    {
        if (!empty($options[self::OPTION_LOCALE])) {
            $loc = $options[self::OPTION_LOCALE];
            if (!is_string($loc) || !preg_match('#[a-z]{2,6}_[A-Z]{2,6}#', $loc)) {
                throw new BadValueException(
                    'Given option "' . self::OPTION_LOCALE
                    . '" is not a valid string. Use "languageCode_countryCode", e.g. "de_DE" or "en_UK".'
                );
            }
            $this->locale = $loc;
            $this->faker = Factory::create($this->locale);
        }

        $attributes_to_exclude = array();
        if (!empty($options[self::OPTION_EXCLUDED_FIELDS])) {
            $excluded = $options[self::OPTION_EXCLUDED_FIELDS];
            if (!is_array($excluded)) {
                throw new BadValueException(
                    'Given option "' . self::OPTION_EXCLUDED_FIELDS
                    . '" is not an array. It should be an array of attribute_names.'
                );
            }
            $attributes_to_exclude = $excluded;
        }

        $type = $document->getType();
        foreach ($type->getAttributes() as $attribute_name => $attribute) {
            if (in_array($attribute_name, $attributes_to_exclude, true)) {
                continue;
            }

            $name = $this->getMethodNameFor($attribute);
            if (null !== $name && is_callable(array($this, $name))) {
                $this->$name($document, $attribute, $options);
            } else {
                $this->setValue($document, $attribute, $attribute->getDefaultValue(), $options);
            }
        }

        if (array_key_exists(self::OPTION_MARK_CLEAN, $options)
            && true === $options[self::OPTION_MARK_CLEAN]
        ) {
            $document->markClean();
        }
    }

    /**
     * Creates an array with fake data for the given type.
     *
     * @param IDocumentType $type type to create fake data for
     * @param array $options For valid options see fake() method
     *
     * @return array of fake data for the given type
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public function fakeData(IDocumentType $type, array $options = array())
    {
        $document = $type->createDocument();
        $this->fake($document, $options);
        return $document->toArray();
    }

    /**
     * Creates a document with fake data for the given type.
     *
     * @param IDocumentType $type type to create documents for
     * @param array $options For valid options see fake() method
     *
     * @return document newly created with fake data
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public function createFakeDocument(IDocumentType $type, array $options = array())
    {
        $options[self::OPTION_MARK_CLEAN] = true;
        $document = $type->createDocument();
        $this->fake($document, $options);
        return $document;
    }

    /**
     * Creates `count` number of documents with fake data for the given type.
     *
     * @param IDocumentType $type type to create documents for
     * @param array $options use `count` for number of documents to create. For other options see fake() method.
     *
     * @return array of new documents with fake data
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public function createFakeDocuments(IDocumentType $type, array $options = array())
    {
        $documents = array();

        $count = 10;
        if (!empty($options[self::OPTION_COUNT])) {
            $cnt = $options[self::OPTION_COUNT];
            if (!is_int($cnt)) {
                throw new BadValueException(
                    'Given option "' . self::OPTION_COUNT
                    . '" is not an integer. Provide a correct value or use fallback to default count.'
                );
            }
            $count = $cnt;
            unset($options[self::OPTION_COUNT]);
        }

        for ($i = 0; $i < $count; $i++) {
             $documents[] = $this->createFakeDocument($type, $options);
        }

        return $documents;
    }

    /**
     * This method fills the document with fake data. You may customize the
     * fake data used for each attribute by using the options array.
     *
     * Supported options:
     * - OPTION_LOCALE: Locale for fake data (e.g. 'en_UK', defaults to 'de_DE').
     * - OPTION_MARK_CLEAN: Calls `$document->markClean()` at the end to prevent
     *                 change events to occur after faking data. Default is false.
     * - OPTION_FIELD_VALUES: array of `attribute_name` => `value` pairs to customize
     *                  fake values per attribute of the given document. You can
     *                  either specify a direct value or provide a closure. The
     *                  closure must return the value you want to set on that attribute.
     * - OPTION_EXCLUDED_FIELDS: Array of attribute_names to excluded from filling
     *                  with fake data.
     * - OPTION_GUESS_PROVIDER_BY_NAME: Boolean true by default. Certain attribute_names
     *                  trigger different providers (e.g. firstname or email).
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public static function fill(IDocument $document, array $options = array())
    {
        $data_generator = new static();
        $data_generator->fake($document, $options);
    }

    /**
     * Creates an array with fake data for the given type.
     *
     * @param IDocumentType $type type to create fake data for
     * @param array $options For valid options see fill() method
     *
     * @return array of fake data for the given type
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public static function createDataFor(IDocumentType $type, array $options = array())
    {
        $data_generator = new static();
        return $data_generator->fakeData($type, $options);
    }

    /**
     * Creates a document with fake data for the given type.
     *
     * @param IDocumentType $type type to create documents for
     * @param array $options For valid options see fill() method
     *
     * @return document newly created with fake data
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public static function createDocument(IDocumentType $type, array $options = array())
    {
        $data_generator = new static();
        return $data_generator->createFakeDocument($type, $options);
    }

    /**
     * Creates `count` number of documents with fake data for the given type.
     *
     * @param IDocumentType $type type to create documents for
     * @param array $options use `count` for number of documents to create. For other options see fill() method.
     *
     * @return array of new documents with fake data
     *
     * @throws \Dat0r\Runtime\Document\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Document\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on AggregateCollection misconfiguration
     */
    public static function createDocuments(IDocumentType $type, array $options = array())
    {
        $data_generator = new DataGenerator();
        return $data_generator->createFakeDocuments($type, $options);
    }

    /**
     * Generates and adds fake data for a Text on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the Text to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addText(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $value = $this->faker->words($this->faker->numberBetween(1, 3), true);

        if ($this->shouldGuessByName($options)) {
            $closure = TextGuesser::guess($attribute->getName(), $this->faker);
            if (!empty($closure) && is_callable($closure)) {
                $value = call_user_func($closure);
            }
        }

        $this->setValue($document, $attribute, $value, $options);
    }

    /**
     * Generates and adds fake data for a TextCollection on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the TextCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addTextCollection(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $text = $this->faker->words($this->faker->numberBetween(1, 3), true);
            if ($this->shouldGuessByName($options)) {
                $closure = TextGuesser::guess($attribute->getName(), $this->faker);
                if (!empty($closure) && is_callable($closure)) {
                    $text = call_user_func($closure);
                }
            }
            $values[] = $text;
        }

        $this->setValue($document, $attribute, $values, $options);
    }

    /**
     * Generates and adds fake data for a Textarea on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the Textarea to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addTextarea(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $text = $this->faker->paragraphs($this->faker->numberBetween(1, 5));
        $this->setValue($document, $attribute, implode(PHP_EOL . PHP_EOL, $text), $options);
    }

    /**
     * Generates and adds fake data for an Number on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the Number to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addNumber(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $this->setValue($document, $attribute, $this->faker->numberBetween(1, 99999), $options);
    }

    /**
     * Generates and adds fake data for an NumberCollection on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the NumberCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addNumberCollection(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $values[] = $this->faker->numberBetween(1, 99999);
        }

        $this->setValue($document, $attribute, $values, $options);
    }

    /**
     * Generates and adds fake data for a KeyValue on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the KeyValue to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addKeyValue(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $values[$this->faker->word] = $this->faker->sentence;
        }

        $this->setValue($document, $attribute, $values, $options);
    }

    /**
     * Generates and adds fake data for a KeyValuesCollection on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the KeyValuesCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addKeyValuesCollection(IDocument $document, IAttribute $attribute, array $options = array())
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

        $this->setValue($document, $attribute, $collection, $options);
    }

    /**
     * Generates and adds fake data for a Boolean on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param IAttribute $attribute an instance of the Boolean to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addBoolean(IDocument $document, IAttribute $attribute, array $options = array())
    {
        $this->setValue($document, $attribute, $this->faker->boolean, $options);
    }

    /**
     * Generates and adds fake data for a aggregate documents.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param AggregateCollection $attribute an instance of the AggregateCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addAggregateCollection(
        IDocument $document,
        AggregateCollection $attribute,
        array $options = array()
    ) {
        $options_clone = $options;
        $document_collection = new DocumentList();
        $aggregate_types = $attribute->getAggregates();

        $number_of_aggregate_types = count($aggregate_types);
        $number_of_new_aggregate_entries = $this->faker->numberBetween(1, 3);

        // add number of documents to reference depending on number of aggregate types
        for ($i = 0; $i < $number_of_aggregate_types; $i++) {
            $number_of_new_aggregate_entries += $this->faker->numberBetween(0, 3);
        }

        // add new documents to collection for aggregate types
        for ($i = 0; $i < $number_of_new_aggregate_entries; $i++) {
            $aggregate_type = $this->faker->randomElement($aggregate_types);
            $new_document = $this->createFakeDocument($aggregate_type, $options_clone);
            $document_collection->addItem($new_document);
        }

        $this->setValue($document, $attribute, $document_collection, $options);
    }

    /**
     * Generates and adds fake data for a ReferenceCollection on a document.
     *
     * @param IDocument $document an instance of the document to fill with fake data.
     * @param ReferenceCollection $attribute an instance of the ReferenceCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addReferenceCollection(
        IDocument $document,
        ReferenceCollection $attribute,
        array $options = array()
    ) {
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

        $referencedTypes = $attribute->getReferences();
        $collection = $attribute->getDefaultValue();

        $numberOfReferencedTypes = count($referencedTypes);
        $numberOfNewReferenceEntries = $this->faker->numberBetween(1, 3);

        // add number of documents to reference depending on number of referenced types
        for ($i = 0; $i < $numberOfReferencedTypes; $i++) {
            $numberOfNewReferenceEntries += $this->faker->numberBetween(0, 3);
        }

        // add new documents to collection for referenced types
        for ($i = 0; $i < $numberOfNewReferenceEntries; $i++) {
            $ref_type = $this->faker->randomElement($referencedTypes);
            $new_document = $this->createFakeDocument($ref_type, $options_clone);
            $collection->addItem($new_document);
        }

        $this->setValue($document, $attribute, $collection, $options);
    }

    /**
     * Sets either given default value or value from option to the given attribute.
     *
     * @param Document $document the document to modify
     * @param string $attribute_name the name of the attribute to set a value for
     * @param mixed $default_value Default value to set.
     * @param array $options Array containing a `attribute_name => $mixed` entry.
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
        IAttribute $attribute,
        $default_value,
        array $options = array()
    ) {
        $attribute_name = $attribute->getName();
        $attribute_options = array();

        if (!empty($options[self::OPTION_FIELD_VALUES])
            && is_array($options[self::OPTION_FIELD_VALUES])
        ) {
            $attribute_options = $options[self::OPTION_FIELD_VALUES];
        }

        if (empty($attribute_options[$attribute_name])) {
            $document->setValue($attribute_name, $default_value);
        } else {
            $option = $attribute_options[$attribute_name];
            if (is_callable($option)) {
                $document->setValue($attribute_name, call_user_func($option));
            } else {
                $document->setValue($attribute_name, $option);
            }
        }
    }

    /**
     * Returns whether or not the fake data generation should be dependant on
     * the attribute_names the used types have.
     *
     * @param array $options array of options to customize fake data creation.
     *
     * @return bool true if the fake data provider should be guessed by attribute_name.
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
     * be generated and added to the given attribute. The pattern is like this:
     *
     * - `addText` for `\Dat0r\Runtime\Attribute\Type\Text`
     * - `addNumberCollection` for `\Dat0r\Runtime\Attribute\Type\NumberCollection`
     * - `addReference` for `\Dat0r\Runtime\Attribute\Type\ReferenceCollection`
     *
     * etc. pp.
     *
     * @param IAttribute $attribute attribute instance to generate fake data for
     *
     * @return string method name to use for fake data addition for given attribute
     */
    protected function getMethodNameFor(IAttribute $attribute)
    {
        $name = null;

        $type = get_class($attribute);
        if (preg_match('/^Dat0r\\\\Runtime\\\\Attribute\\\\Type\\\\(.*)$/', $type, $matches)) {
            $name = 'add' . $matches[1];
        }

        return $name;
    }
}
