<?php

namespace Dat0r\Tests\Runtime\Entity;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Entity\Transform\Fixtures\TestTransformer;
use Dat0r\Tests\Runtime\Entity\Transform\Fixtures\EmbedSpecifications;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\Article;

class TransformerTest extends TestCase
{
    public function testCreate()
    {
        $transformer = new TestTransformer();

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Entity\\Transform\\ITransformer', $transformer);
        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $transformer->getOptions());
        $this->assertEquals('bar', $transformer->getOption('foo'));
    }

    /**
     * @dataProvider provideTestEntity
     */
    public function testTransform(Article $entity)
    {
        $transformer = new TestTransformer();
        $spec_container = new EmbedSpecifications();
        $transformed_data = $transformer->transform($entity, $spec_container);

        $this->assertEquals($entity->getValue('headline'), $transformed_data['title']);
        $this->assertEquals($entity->getValue('author'), $transformed_data['author']);
    }

    public function provideTestEntity()
    {
        $type = new ArticleType();
        $test_entity = $type->createEntity(
            array(
                'headline' => 'This is incredible stuff!',
                'author' => 'Thorsten Schmitt-Rink',
                'email' => 'thorsten.schmitt-rink@example.com',
                'content' => 'This is some kind of very valueable and incredible content.',
                'enabled' => true,
                'click_count' => 23,
                'images' => array(5, 23, 42),
                'keywords' => array('incredible', 'valueable'),
                'meta' => array('state' => 'edit'),
                'paragraphs' => array(
                    array(
                        'title' => 'This is an amazing paragraph',
                        'content' => 'Bob! This is just in incredible!',
                        '@type' => '\\Dat0r\\Tests\\Runtime\\Fixtures\\Paragraph'
                    )
                )
            )
        );

        return array(array($test_entity));
    }
}
