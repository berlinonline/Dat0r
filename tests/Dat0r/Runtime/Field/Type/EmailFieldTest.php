<?php

namespace Dat0r\Tests\Runtime\Field;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Field\Type\EmailField;
use Dat0r\Runtime\Validator\Result\IIncident;

class EmailFieldTest extends TestCase
{
    public function testCreate()
    {
        $email_field = new EmailField('email');
        $this->assertEquals($email_field->getName(), 'email');
    }

    public function testCreateValueHolder()
    {
        $email = 'foo.bar@example.com';
        $email_field = new EmailField('email');
        $value_holder = $email_field->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\EmailValueHolder', $value_holder);
        $value_holder->setValue($email);
        $this->assertEquals($email, $value_holder->getValue());
    }

    public function testValidationSuccess()
    {
        $email_field = new EmailField('email');
        $result = $email_field->getValidator()->validate('foo.bar@example.com');
        $this->assertEquals($result->getSeverity(), IIncident::SUCCESS);
    }

    public function testValidationError()
    {
        $email_field = new EmailField('email');
        $result = $email_field->getValidator()->validate('foo.bar.com');
        $this->assertEquals($result->getSeverity(), IIncident::ERROR);
    }
}
