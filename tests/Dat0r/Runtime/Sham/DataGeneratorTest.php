<?php

namespace Dat0r\Tests\Runtime\Sham;

use Dat0r\Runtime\Sham\DataGenerator;
use Dat0r\Runtime\Document\IDocument;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Module\Fixtures\RootModule;

class DataGeneratorTest extends TestCase
{
    protected $module;
    protected $document;

    public function setUp()
    {
        $this->module = RootModule::getInstance();
        $this->document = $this->module->createDocument();
    }

    public function testDefaultDocument()
    {
        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\IDocument', $this->document);
        $this->assertEquals('Article', $this->module->getName());
        $this->assertEquals(
            11,
            $this->module->getFields()->getSize(),
            'Number of fields is unexpected. Please adjust tests if new fields were introduced.'
        );
        $this->assertEquals(
            $this->module->getFields()->getSize(),
            count($this->module->getFields()),
            'Number of fields should be equal independant of used count method.'
        );
        $this->assertTrue(
            $this->document->isClean(),
            'Document should have no changes prior filling it with fake data'
        );
        $this->assertTrue(
            count($this->document->getChanges()) === 0,
            'Document should not contain changes prior test.'
        );
    }

    public function testFillDocument()
    {
        DataGenerator::fill($this->document);

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );
    }

    public function testFillDocumentClean()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_MARK_CLEAN => true));

        $this->assertTrue(
            $this->document->isClean(),
            'Document has changes, but the given flag should have prevented that.'
        );
        $this->assertTrue(count($this->document->getChanges()) === 0);
    }

    public function testFillDocumentWithClosure()
    {
        DataGenerator::fill(
            $this->document,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'author' => function () {
                        return 'trololo';
                    }
                )
            )
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );
        $this->assertTrue($this->document->getValue('author') === 'trololo');
    }

    public function testFillDocumentWithCallable()
    {
        DataGenerator::fill(
            $this->document,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'author' => array($this, 'getTrololo')
                )
            )
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );
        $this->assertTrue($this->document->getValue('author') === 'trololo');
    }

    public function testFillDocumentWithStaticCallable()
    {
        DataGenerator::fill(
            $this->document,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'author' => 'Dat0r\\Tests\\Runtime\\Sham\\DataGeneratorTest::getStaticTrololo'
                )
            )
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );
        $this->assertTrue($this->document->getValue('author') === 'trololo');
    }

    public function testFillDocumentWithMultipleClosures()
    {
        $faker = \Faker\Factory::create('en_UK');
        $fake_author = function () use ($faker) {
            return $faker->name;
        };
        $fake_headline = function () use ($faker) {
            return $faker->sentence;
        };
        $fake_content = function () use ($faker) {
            return $faker->paragraphs(4, true);
        };

        DataGenerator::fill(
            $this->document,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'headline' => $fake_headline,
                    'content' => $fake_content,
                    'author' => $fake_author,
                    'images' => array(1,2,3,4),
                    'clickCount' => 1337,
                    'non_existant' => 'asdf'
                )
            )
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );
        $this->assertTrue(count($this->document->getValue('images')) === 4);
        $this->assertTrue($this->document->getValue('clickCount') === 1337);
    }

    public function testFillDocumentBoolean()
    {
        DataGenerator::fill($this->document);

        $this->assertTrue(is_bool($this->document->getValue('enabled')), 'Enabled field should have a boolean value.');
    }

    public function testFillDocumentTextCollection()
    {
        DataGenerator::fill($this->document);

        $this->assertFalse(
            $this->document->isClean(),
            'Document has changes, but the given flag should have prevented that.'
        );

        $this->assertTrue(
            is_array($this->document->getValue('keywords')),
            'Keywords value should be an array as that field is an instance of TextCollectionField'
        );

        $this->assertGreaterThanOrEqual(
            1,
            count($this->document->getValue('keywords')),
            'At least one keyword should be set.'
        );
    }

    public function testFillDocumentAggregate()
    {
        $data = DataGenerator::createDataFor($this->module);
        $this->assertTrue(is_array($data['paragraph']), 'The Article should have a paragraph.');
        $paragraph_data = $data['paragraph'][0];

        $this->assertArrayHasKey('title', $paragraph_data, 'The Paragraph should have a title field.');
        $this->assertTrue(!empty($paragraph_data['title']), 'The title of the Paragraph should not be empty.');
        $this->assertArrayHasKey('content', $paragraph_data, 'The Paragraph should have a content field.');
    }

    public function testFillDocumentGuessTextFieldEmail()
    {
        DataGenerator::fill($this->document);

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );

        $email = $this->document->getValue('email');
        $this->assertEquals($email, filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    public function testFillDocumentGuessTextFieldAuthor()
    {
        DataGenerator::fill(
            $this->document,
            array(DataGenerator::OPTION_LOCALE => 'de_DE')
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );

        $author = $this->document->getValue('author');
        $name_parts = explode(' ', $author);
        $len = count($name_parts);
        for ($i = 0; $i < $len; $i++) {
            if ($i == 0) {
                continue; // first part is a prefixName or a firstName
            }
            $candidate = explode('-', $name_parts[$i]);
            // split firstName-firstName and lastName-lastName and get just the first name
            $name_parts[$i] = array_shift($candidate);
        }
        // should now be something like '(prefix )firstname lastname...'
        $this->assertTrue(is_array($name_parts) && count($name_parts) >= 1);

        $reflectionClass = new \ReflectionClass('\Faker\Provider\de_DE\Person');
        $prefixMale = $reflectionClass->getProperty('prefixMale');
        $prefixFemale = $reflectionClass->getProperty('prefixFemale');
        $firstName = $reflectionClass->getProperty('firstName');
        $lastName = $reflectionClass->getProperty('lastName');
        $prefixMale->setAccessible(true);
        $prefixFemale->setAccessible(true);
        $firstName->setAccessible(true);
        $lastName->setAccessible(true);

        $available_name_parts = array_merge(
            $prefixMale->getValue(),
            $prefixFemale->getValue(),
            $firstName->getValue(),
            $lastName->getValue()
        );

        $this->assertContains(
            $name_parts[0],
            $available_name_parts,
            'Prefix or firstName should be part of the generated author name.'
        );

        if (count($name_parts) > 1) {
            $this->assertContains(
                $name_parts[1],
                $available_name_parts,
                'firstName or lastName should be part of the generated author name.'
            );
        }
    }

    public function testFillDocumentGuessTextFieldAuthorDisabled()
    {
        DataGenerator::fill(
            $this->document,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_GUESS_PROVIDER_BY_NAME => false
            )
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );

        $author = $this->document->getValue('author');
        $name_parts = explode(' ', $author);
        $len = count($name_parts);
        for ($i = 0; $i < $len; $i++) {
            if ($i == 0) {
                continue; // first part is a prefixName or a firstName
            }
            $candidate = explode('-', $name_parts[$i]);
            // split firstName-firstName and lastName-lastName and get just the first name
            $name_parts[$i] = array_shift($candidate);
        }
        // should now be something like '(prefix )firstname lastname...'
        $this->assertTrue(is_array($name_parts) && count($name_parts) >= 1);

        $reflectionClass = new \ReflectionClass('\Faker\Provider\de_DE\Person');
        $prefixMale = $reflectionClass->getProperty('prefixMale');
        $prefixFemale = $reflectionClass->getProperty('prefixFemale');
        $firstName = $reflectionClass->getProperty('firstName');
        $lastName = $reflectionClass->getProperty('lastName');
        $prefixMale->setAccessible(true);
        $prefixFemale->setAccessible(true);
        $firstName->setAccessible(true);
        $lastName->setAccessible(true);

        $available_name_parts = array_merge(
            $prefixMale->getValue(),
            $prefixFemale->getValue(),
            $firstName->getValue(),
            $lastName->getValue()
        );

        $this->assertNotContains(
            $name_parts[0],
            $available_name_parts,
            'Prefix or firstName should NOT be part of the generated author name.'
        );

        if (count($name_parts) > 1) {
            $this->assertNotContains(
                $name_parts[1],
                $available_name_parts,
                'firstName or lastName should NOT be part of the generated author name.'
            );
        }
    }

    public function testFillDocumentIgnoreField()
    {
        $this->assertEquals(11, $this->module->getFields()->getSize());
        $excluded_fields = array('author', 'clickCount', 'enabled', 'references');
        DataGenerator::fill(
            $this->document,
            array(
                DataGenerator::OPTION_EXCLUDED_FIELDS => array_merge($excluded_fields, array('non_existant'))
            )
        );

        $this->assertFalse(
            $this->document->isClean(),
            'Document has no changes, but should have been filled with fake data.'
        );
        $this->assertEquals(
            $this->module->getFields()->getSize() - count($excluded_fields),
            count($this->document->getChanges()),
            count($excluded_fields) . ' fields should have been ignored.'
        );

        $this->setExpectedException('\Dat0r\Common\Error\RuntimeException');
        // @codeCoverageIgnoreStart
        $this->assertFalse($this->document->getValue('non_existant'));
    }// @codeCoverageIgnoreEnd

    /**
     * @expectedException Dat0r\Common\Error\BadValueException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_LOCALE => 'trololo'));
    }

    /**
     * @expectedException Dat0r\Common\Error\BadValueException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill2()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_LOCALE => 1));
    }

    /**
     * @expectedException Dat0r\Common\Error\BadValueException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill3()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_LOCALE => new \stdClass()));
    }

    public function testCreateDataFor()
    {
        $data = DataGenerator::createDataFor(
            $this->module,
            array(
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'non_existant' => 'trololo'
                )
            )
        );

        $this->assertTrue(is_array($data), 'Returned data should be an array.');
        $this->assertTrue(!empty($data), 'Returned data array should not be empty.');
        $this->assertArrayHasKey('author', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('headline', $data);
        $this->assertArrayHasKey('clickCount', $data);
        $this->assertArrayHasKey('content', $data);
        $this->assertFalse(
            isset($data['non_existant']),
            'Returned array should not have a value for the non_existant field.'
        );
    }

    public function testCreateDocument()
    {
        $document = DataGenerator::createDocument($this->module);

        $this->assertTrue($document->isClean(), 'New document should have no changes.');
        $this->assertTrue(0 === count($document->getChanges()), 'New document should have no changes.');
    }

    public function testCreateDocuments()
    {
        $num_documents = 30;
        $documents = DataGenerator::createDocuments(
            $this->module,
            array(
                DataGenerator::OPTION_COUNT => $num_documents,
                DataGenerator::OPTION_LOCALE => 'fr_FR'
            )
        );

        $this->assertTrue($num_documents === count($documents));

        for ($i = 0; $i < $num_documents; $i++) {
            $document = $documents[$i];
            $this->assertTrue($document->isClean(), "New document $i should have no changes.");
            $this->assertTrue(0 === count($document->getChanges()), "New document $i should have no changes.");
        }
    }

    public function getTrololo()
    {
        return 'trololo';
    }

    public static function getStaticTrololo()
    {
        return 'trololo';
    }
}
