<?php

namespace Dat0r\Tests\Runtime\Attribute\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Type\Email;
use Dat0r\Runtime\Validator\Result\IIncident;

class EmailTest extends TestCase
{
    public function testCreate()
    {
        $email_attribute = new Email('email');
        $this->assertEquals($email_attribute->getName(), 'email');
    }

    public function testCreateValue()
    {
        $email = 'foo.bar@example.com';
        $email_attribute = new Email('email');
        $value = $email_attribute->createValue();
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Value\\Type\\EmailValue', $value);
        $value->set($email);
        $this->assertEquals($email, $value->get());
    }

    public function testValidationSuccess()
    {
        $email_attribute = new Email('email');
        $result = $email_attribute->getValidator()->validate('foo.bar@example.com');
        $this->assertEquals($result->getSeverity(), IIncident::SUCCESS);
    }

    public function testValidationError()
    {
        $email_attribute = new Email('email');
        $result = $email_attribute->getValidator()->validate('foo.bar.com');
        $this->assertEquals($result->getSeverity(), IIncident::ERROR);
    }
}
