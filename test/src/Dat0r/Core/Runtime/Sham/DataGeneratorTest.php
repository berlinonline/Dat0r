<?php

namespace Dat0r\Tests\Core\Runtime\Sham;

use Dat0r\Core\Runtime\Sham\DataGenerator;
use Dat0r\Core\Runtime\Document\IDocument;

use Dat0r\Tests\Core\BaseTest;
use Dat0r\Tests\Core\Runtime\Module\RootModule;

class DataGeneratorTest extends BaseTest
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
        $this->assertInstanceOf('Dat0r\\Core\Runtime\\Document\\Document', $this->document);
        $this->assertEquals('Article', $this->module->getName());
        $this->assertEquals(6, $this->module->getFields()->getSize(), 'Number of fields is unexpected. Please adjust tests if new fields were introduced.');
        $this->assertEquals($this->module->getFields()->getSize(), count($this->module->getFields()), 'Number of fields should be equal independant of used count method.');
        $this->assertTrue($this->document->isClean(), 'Document should have no changes prior filling it with fake data');
        $this->assertTrue(count($this->document->getChanges()) === 0, 'Document should not contain changes prior test.');
    }

    public function testFillDocument()
    {
        DataGenerator::fill($this->document);

        $this->assertFalse($this->document->isClean(), 'Document has no changes, but should have been filled with fake data.');
        $this->assertTrue(count($this->document->getChanges()) === $this->module->getFields()->getSize());
    }

    public function testFillDocumentClean()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_MARK_CLEAN => true));

        $this->assertTrue($this->document->isClean(), 'Document has changes, but the given flag should have prevented that.');
        $this->assertTrue(count($this->document->getChanges()) === 0);
    }

    public function testFillDocumentWithClosure()
    {
        DataGenerator::fill($this->document, array(
            DataGenerator::OPTION_LOCALE => 'de_DE',
            DataGenerator::OPTION_FIELD_VALUES => array(
                'author' => function() { return 'trololo'; }
            )
        ));

        $this->assertFalse($this->document->isClean(), 'Document has no changes, but should have been filled with fake data.');
        $this->assertTrue(count($this->document->getChanges()) === $this->module->getFields()->getSize());
        $this->assertTrue($this->document->getValue('author') === 'trololo');
    }

    public function testFillDocumentWithMultipleClosures()
    {
        $faker = \Faker\Factory::create('en_UK');
        $fake_author = function() use ($faker) {
            return $faker->name;
        };
        $fake_headline = function() use ($faker) {
            return $faker->sentence;
        };
        $fake_content = function() use ($faker) {
            return $faker->paragraphs(4, true);
        };

        DataGenerator::fill($this->document, array(
            DataGenerator::OPTION_LOCALE => 'de_DE',
            DataGenerator::OPTION_FIELD_VALUES => array(
                'headline' => $fake_headline,
                'content' => $fake_content,
                'author' => $fake_author,
                'images' => array(1,2,3,4),
                'clickCount' => 1337,
                'non_existant' => 'asdf'
            )
        ));

        $this->assertFalse($this->document->isClean(), 'Document has no changes, but should have been filled with fake data.');
        $this->assertTrue(count($this->document->getChanges()) === $this->module->getFields()->getSize());
        $this->assertTrue(count($this->document->getValue('images')) === 4);
        $this->assertTrue($this->document->getValue('clickCount') === 1337);
    }

    public function testFillDocumentIgnoreField()
    {
        DataGenerator::fill($this->document, array(
            DataGenerator::OPTION_EXCLUDED_FIELDS => array('author', 'clickCount', 'non_existant')
        ));

        $this->assertFalse($this->document->isClean(), 'Document has no changes, but should have been filled with fake data.');
        $this->assertEquals($this->module->getFields()->getSize() -2, count($this->document->getChanges()), '2 Fields should have been ignored.');
        $this->setExpectedException('\Dat0r\Core\Runtime\Module\InvalidFieldException');
        // @codeCoverageIgnoreStart
        $this->assertFalse($this->document->getValue('non_existant'));
    }// @codeCoverageIgnoreEnd

    /**
     * @expectedException \Dat0r\Core\Runtime\Document\InvalidValueException
     * @codeCoverageIgnore
     */
    public function testFillDocumentWithInvalidFieldValue()
    {
        DataGenerator::fill($this->document, array(
            DataGenerator::OPTION_LOCALE => 'de_DE',
            DataGenerator::OPTION_FIELD_VALUES => array(
                'clickCount' => 'trololo'
            )
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_LOCALE => 'trololo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill2()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_LOCALE => 1));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill3()
    {
        DataGenerator::fill($this->document, array(DataGenerator::OPTION_LOCALE => new \stdClass()));
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
        $documents = DataGenerator::createDocuments($this->module, array(
            DataGenerator::OPTION_COUNT => $num_documents,
            DataGenerator::OPTION_LOCALE => 'fr_FR'
        ));

        $this->assertTrue($num_documents === count($documents));

        for ($i = 0; $i < $num_documents; $i++)
        {
            $document = $documents[$i];
            $this->assertTrue($document->isClean(), "New document $i should have no changes.");
            $this->assertTrue(0 === count($document->getChanges()), "New document $i should have no changes.");
        }
    }
}
