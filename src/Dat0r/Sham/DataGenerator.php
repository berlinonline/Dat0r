<?php

namespace Dat0r\Sham;

use Faker\Factory;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\Entity\EntityInterface;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Sham\TextGuesser;

/**
 * Sham\DataGenerator is a class that is able to create or fill entities
 * containing fake data.
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
     * name of options array key to use to mark changed entities as clean
     */
    const OPTION_MARK_CLEAN = 'mark_clean';

    /**
     * name of options array key to use to set the locale used for fake data generation
     */
    const OPTION_LOCALE = 'locale';

    /**
     * name of options array key to use to set the number of entities to generate
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
     * This method fills the given entity with fake data. You may customize
     * the fake data generation used for each attribute by using the options array.
     *
     * Supported options:
     * - OPTION_LOCALE: Locale for fake data (e.g. 'en_UK', defaults to 'de_DE').
     * - OPTION_MARK_CLEAN: Calls `$entity->markClean()` at the end to prevent
     *                 change events to occur after faking data. Default is false.
     * - OPTION_FIELD_VALUES: array of `attribute_name` => `value` pairs to customize
     *                  fake values per attribute of the given entity. You can
     *                  either specify a direct value or provide a closure. The
     *                  closure must return the value you want to set on that attribute.
     * - OPTION_EXCLUDED_FIELDS: Array of attribute_names to excluded from filling
     *                  with fake data.
     * - OPTION_GUESS_PROVIDER_BY_NAME: Boolean true by default. Certain attribute_names
     *                  trigger different providers (e.g. firstname or email).
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public function fake(EntityInterface $entity, array $options = array())
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

        $type = $entity->getType();
        foreach ($type->getAttributes() as $attribute_name => $attribute) {
            if (in_array($attribute_name, $attributes_to_exclude, true)) {
                continue;
            }

            $name = $this->getMethodNameFor($attribute);
            if (null !== $name && is_callable(array($this, $name))) {
                $this->$name($entity, $attribute, $options);
            } else {
                $this->setValue($entity, $attribute, $attribute->getDefaultValue(), $options);
            }
        }

        if (array_key_exists(self::OPTION_MARK_CLEAN, $options)
            && true === $options[self::OPTION_MARK_CLEAN]
        ) {
            $entity->markClean();
        }
    }

    /**
     * Creates an array with fake data for the given type.
     *
     * @param EntityTypeInterface $type type to create fake data for
     * @param array $options For valid options see fake() method
     *
     * @return array of fake data for the given type
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public function fakeData(EntityTypeInterface $type, array $options = array())
    {
        $entity = $type->createEntity();
        $this->fake($entity, $options);
        return $entity->toArray();
    }

    /**
     * Creates a entity with fake data for the given type.
     *
     * @param EntityTypeInterface $type type to create entities for
     * @param array $options For valid options see fake() method
     *
     * @return entity newly created with fake data
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public function createFakeEntity(EntityTypeInterface $type, array $options = array())
    {
        $options[self::OPTION_MARK_CLEAN] = true;
        $entity = $type->createEntity();
        $this->fake($entity, $options);
        return $entity;
    }

    /**
     * Creates `count` number of entities with fake data for the given type.
     *
     * @param EntityTypeInterface $type type to create entities for
     * @param array $options use `count` for number of entities to create. For other options see fake() method.
     *
     * @return array of new entities with fake data
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public function createFakeEntities(EntityTypeInterface $type, array $options = array())
    {
        $entities = array();

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
             $entities[] = $this->createFakeEntity($type, $options);
        }

        return $entities;
    }

    /**
     * This method fills the entity with fake data. You may customize the
     * fake data used for each attribute by using the options array.
     *
     * Supported options:
     * - OPTION_LOCALE: Locale for fake data (e.g. 'en_UK', defaults to 'de_DE').
     * - OPTION_MARK_CLEAN: Calls `$entity->markClean()` at the end to prevent
     *                 change events to occur after faking data. Default is false.
     * - OPTION_FIELD_VALUES: array of `attribute_name` => `value` pairs to customize
     *                  fake values per attribute of the given entity. You can
     *                  either specify a direct value or provide a closure. The
     *                  closure must return the value you want to set on that attribute.
     * - OPTION_EXCLUDED_FIELDS: Array of attribute_names to excluded from filling
     *                  with fake data.
     * - OPTION_GUESS_PROVIDER_BY_NAME: Boolean true by default. Certain attribute_names
     *                  trigger different providers (e.g. firstname or email).
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public static function fill(EntityInterface $entity, array $options = array())
    {
        $data_generator = new static();
        $data_generator->fake($entity, $options);
    }

    /**
     * Creates an array with fake data for the given type.
     *
     * @param EntityTypeInterface $type type to create fake data for
     * @param array $options For valid options see fill() method
     *
     * @return array of fake data for the given type
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public static function createDataFor(EntityTypeInterface $type, array $options = array())
    {
        $data_generator = new static();
        return $data_generator->fakeData($type, $options);
    }

    /**
     * Creates a entity with fake data for the given type.
     *
     * @param EntityTypeInterface $type type to create entities for
     * @param array $options For valid options see fill() method
     *
     * @return entity newly created with fake data
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public static function createEntity(EntityTypeInterface $type, array $options = array())
    {
        $data_generator = new static();
        return $data_generator->createFakeEntity($type, $options);
    }

    /**
     * Creates `count` number of entities with fake data for the given type.
     *
     * @param EntityTypeInterface $type type to create entities for
     * @param array $options use `count` for number of entities to create. For other options see fill() method.
     *
     * @return array of new entities with fake data
     *
     * @throws \Dat0r\Runtime\Entity\InvalidValueException in case of fake data being invalid for the given attribute
     * @throws \Dat0r\Runtime\Entity\BadValueException in case of invalid locale option string
     * @throws \Dat0r\Common\Error\RuntimeException on EmbeddedEntityListAttribute misconfiguration
     */
    public static function createEntities(EntityTypeInterface $type, array $options = array())
    {
        $data_generator = new DataGenerator();
        return $data_generator->createFakeEntities($type, $options);
    }

    /**
     * Generates and adds fake data for a Text on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the Text to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addText(EntityInterface $entity, AttributeInterface $attribute, array $options = array())
    {
        if ($this->shouldGuessByName($options)) {
            $value = TextGuesser::guess($attribute->getName(), $this->faker);
        } else {
            $value = $this->faker->words($this->faker->numberBetween(1, 3), true);
        }

        $this->setValue($entity, $attribute, $value, $options);
    }

    /**
     * Generates and adds fake data for a TextCollection on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the TextCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addTextList(
        EntityInterface $entity,
        AttributeInterface $attribute,
        array $options = array()
    ) {
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

        $this->setValue($entity, $attribute, $values, $options);
    }

    /**
     * Generates and adds fake data for a Textarea on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the Textarea to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addTextarea(EntityInterface $entity, AttributeInterface $attribute, array $options = array())
    {
        $text = $this->faker->paragraphs($this->faker->numberBetween(1, 5));
        $this->setValue($entity, $attribute, implode(PHP_EOL . PHP_EOL, $text), $options);
    }

    /**
     * Generates and adds fake data for an Number on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the Number to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addNumber(EntityInterface $entity, AttributeInterface $attribute, array $options = array())
    {
        $this->setValue($entity, $attribute, $this->faker->numberBetween(1, 99999), $options);
    }

    /**
     * Generates and adds fake data for an NumberCollection on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the NumberCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addNumberList(
        EntityInterface $entity,
        AttributeInterface $attribute,
        array $options = array()
    ) {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $values[] = $this->faker->numberBetween(1, 99999);
        }

        $this->setValue($entity, $attribute, $values, $options);
    }

    /**
     * Generates and adds fake data for a KeyValue on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the KeyValue to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addKeyValue(EntityInterface $entity, AttributeInterface $attribute, array $options = array())
    {
        $values = array();

        $number_of_values = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $number_of_values; $i++) {
            $values[$this->faker->word] = $this->faker->sentence;
        }

        $this->setValue($entity, $attribute, $values, $options);
    }

    /**
     * Generates and adds fake data for a KeyValuesCollection on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the KeyValuesCollection to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addKeyValuesCollection(
        EntityInterface $entity,
        AttributeInterface $attribute,
        array $options = array()
    ) {
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

        $this->setValue($entity, $attribute, $collection, $options);
    }

    /**
     * Generates and adds fake data for a Boolean on a entity.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param AttributeInterface $attribute an instance of the Boolean to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addBoolean(EntityInterface $entity, AttributeInterface $attribute, array $options = array())
    {
        $this->setValue($entity, $attribute, $this->faker->boolean, $options);
    }

    /**
     * Generates and adds fake data for a embed entities.
     *
     * @param EntityInterface $entity an instance of the entity to fill with fake data.
     * @param EmbeddedEntityListAttribute $attribute instance of the EmbeddedEntityListAttribute to fill with fake data.
     * @param array $options array of options to customize fake data creation.
     *
     * @return void
     */
    protected function addEmbeddedEntityList(
        EntityInterface $entity,
        EmbeddedEntityListAttribute $attribute,
        array $options = array()
    ) {
        $options_clone = $options;
        $entity_collection = new EntityList();
        $embed_types = $attribute->getEntityTypes();

        $number_of_embed_types = count($embed_types);
        $number_of_new_embed_entries = $this->faker->numberBetween(1, 3);

        // add number of entities to reference depending on number of embed types
        for ($i = 0; $i < $number_of_embed_types; $i++) {
            $number_of_new_embed_entries += $this->faker->numberBetween(0, 3);
        }

        // add new entities to collection for embed types
        for ($i = 0; $i < $number_of_new_embed_entries; $i++) {
            $embed_type = $this->faker->randomElement($embed_types);
            $new_entity = $this->createFakeEntity($embed_type, $options_clone);
            $entity_collection->addItem($new_entity);
        }
        $this->setValue($entity, $attribute, $entity_collection, $options);
    }

    /**
     * Sets either given default value or value from option to the given attribute.
     *
     * @param Entity $entity the entity to modify
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
        EntityInterface $entity,
        AttributeInterface $attribute,
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
            $entity->setValue($attribute_name, $default_value);
        } else {
            $option = $attribute_options[$attribute_name];
            if (is_callable($option)) {
                $entity->setValue($attribute_name, call_user_func($option));
            } else {
                $entity->setValue($attribute_name, $option);
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
     *
     * etc. pp.
     *
     * @param AttributeInterface $attribute attribute instance to generate fake data for
     *
     * @return string method name to use for fake data addition for given attribute
     */
    protected function getMethodNameFor(AttributeInterface $attribute)
    {
        $attribute_class_parts = explode('\\', get_class($attribute));
        $attribute_class = array_pop($attribute_class_parts);
        $clean_type_name = preg_replace('#Attribute$#', '', $attribute_class);

        return 'add' . ucfirst($clean_type_name);
    }
}
