<?php

namespace Dat0r\Tests\Runtime\Entity;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Entity\Fixtures\EntityTestProxy;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;

use Dat0r\Common\Collection\ArrayList;
use Dat0r\Runtime\Entity\EntityList;

class EntityTest extends TestCase
{
    public function testCreateEntity()
    {
        $type = new ArticleType();
        $entity = $type->createEntity(array(
            'headline' => 'hello world!'
        ));

        $this->assertTrue($entity->isValid());
        $this->assertEquals('hello world!', $entity->getValue('headline'));
    }

    public function testInvalidValue()
    {
        $type = new ArticleType();
        $entity = $type->createEntity(array(
            'headline' => 'hel'
        ));

        $this->assertFalse($entity->isValid());
        $this->assertEquals(null, $entity->getValue('headline'));
    }
}
