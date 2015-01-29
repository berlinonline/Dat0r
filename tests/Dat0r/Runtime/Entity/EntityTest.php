<?php

namespace Dat0r\Tests\Runtime\Entity;

use Dat0r\Common\Collection\ArrayList;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Tests\Runtime\Entity\Fixtures\EntityTestProxy;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\TestCase;

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

    public function testToNative()
    {
        $values = [
            'headline' => 'headline',
            'content' => 'content',
            'click_count' => 123,
            'author' => 'Some Author',
            'email' => 'some.author@example.com',
            'birthday' => '2014-12-31T12:45:55.123456+01:00',
            'images' => [ 1, 2, 3 ],
            'keywords' => [ 'some', 'keywords' ],
            'enabled' => true,
            'content_objects' => [],
            'meta' => [],
            'workflow_ticket' => []
        ];

        $type = new ArticleType();
        $entity = $type->createEntity($values);

        $this->assertTrue($entity->isValid());

        $result = $entity->toNative();

        $this->assertEquals(array_keys($values), array_keys($result));
        $this->assertEquals('headline', $result['headline']);
        $this->assertEquals(123, $result['click_count']);
        $this->assertEquals('some.author@example.com', $result['email']);
        $this->assertEquals('2014-12-31T11:45:55.123456+00:00', $result['birthday']); // utc
        $this->assertTrue($result['enabled']);
        $this->assertTrue(is_array($result['content_objects']));
        $this->assertTrue(is_array($result['meta']));
        $this->assertTrue(is_array($result['workflow_ticket']));
    }

    public function testToNativeReconstitution()
    {
        $values = [
            'headline' => 'headline',
            'content' => 'content',
            'click_count' => 123,
            'author' => 'Some Author',
            'email' => 'some.author@example.com',
            'birthday' => '2014-12-31T12:45:55.123456+01:00',
            'images' => [ 1, 2, 3 ],
            'keywords' => [ 'some', 'keywords' ],
            'enabled' => true,
            'content_objects' => [],
            'meta' => [],
            'workflow_ticket' => []
        ];

        $type = new ArticleType();
        $entity = $type->createEntity($values);

        $this->assertTrue($entity->isValid());

        $result = $entity->toNative();

        $new_entity = $type->createEntity($result);

        $this->assertTrue($new_entity->isEqualTo($entity));
    }
}
