<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Type\Email;
use Dat0r\Runtime\Attribute\Validator\Result\IIncident;

class EmailTest extends TestCase
{
    public function testCreate()
    {
        $email_attribute = new Email('email');
        $this->assertEquals($email_attribute->getName(), 'email');
    }

    public function testCreateValueHolder()
    {
        $email = 'foo.bar@example.com';
        $email_attribute = new Email('email');
        $value_holder = $email_attribute->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\ValueHolder\\Type\\EmailValueHolder', $value_holder);
        $value_holder->setValue($email);
        $this->assertEquals($email, $value_holder->getValue());
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
