<?php

namespace Dat0r\Tests\Runtime\Attribute\EmailList;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\EmailList\EmailListAttribute;
use Dat0r\Runtime\Attribute\EmailList\EmailListValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class EmailListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new EmailListAttribute('emails');
        $this->assertEquals($attribute->getName(), 'emails');
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ 'foo@bar.com' => 'bar' ];

        $attribute = new EmailListAttribute('emails', [ EmailListAttribute::OPTION_DEFAULT_VALUE => $data ]);

        $valueholder = $attribute->createValueHolder();
        $this->assertInstanceOf(EmailListValueHolder::CLASS, $valueholder);
        $this->assertEquals($data, $valueholder->getValue());
    }

    public function testCastToArrayWhenSettingSingleValueWorks()
    {
        $attribute = new EmailListAttribute('emails');
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('foo@bar.com');
        $this->assertEquals([ 'foo@bar.com' => '' ], $valueholder->getValue());
    }

    public function testSettingInvalidValueFails()
    {
        $data = [ 'foobarcom' => 'bar' ];

        $attribute = new EmailListAttribute('emails');

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testValueComparison()
    {
        $data = [ 'foo@bar.com' => 'bar' ];
        $foo = $data;
        $bar = $data;
        $bar['asdf@example.com'] = 'omgomgomg';

        $attribute = new EmailListAttribute('emails', [ EmailListAttribute::OPTION_DEFAULT_VALUE => $data ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            'bar@foo.com' => '15',
            'foo@bar.com' => '1234567890',
        ];

        $attribute = new EmailListAttribute('emailslabellength', [
            EmailListAttribute::OPTION_MIN => 3,
            EmailListAttribute::OPTION_MAX => 5
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testMaxCountConstraint()
    {
        $data = [ 'foo@bar.com' => 'bar', 'blah@exmaple.com' => 'blub' ];

        $attribute = new EmailListAttribute('emailsmaxcount', [
            EmailListAttribute::OPTION_MAX_COUNT => 1
        ]);

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $attribute->getNullValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ 'foo@bar.com' => 'bar' ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }

    public function testToNativeRoundtripWithBooleanFlags()
    {
        $emails = [ 'foo@bar.com' => 'some name', 'blah@blub.com' => 'yeah right' ];
        $attribute = new EmailListAttribute('emails', []);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($emails);
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($emails, $valueholder->getValue());
        $this->assertEquals($emails, $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($emails, $valueholder->toNative());
        $this->assertEquals($emails, $valueholder->getValue());
    }

    public function testAllowedLabelsConstraintFails()
    {
        $attribute = new EmailListAttribute('emails', [
            EmailListAttribute::OPTION_ALLOWED_LABELS => [ 'bar' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo@bar.com' => 'blah']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedEmailsConstraintFails()
    {
        $attribute = new EmailListAttribute('emails', [
            EmailListAttribute::OPTION_ALLOWED_EMAILS => [ 'foo@bar.com' => 'asdf' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['bar@foo.com' => 'asdf']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedPairsConstraintFails()
    {
        $attribute = new EmailListAttribute('emails', [
            EmailListAttribute::OPTION_ALLOWED_PAIRS => [ 'foo@bar.com' => 'foo' ]
        ]);

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo@bar.de' => 'foo', 'foo@bar.com' => 'fo' ]);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);

        $attribute = new EmailListAttribute('emailinvalidintegerdefaultvalue', [
            EmailListAttribute::OPTION_MIN => 1,
            EmailListAttribute::OPTION_MAX => 5,
            EmailListAttribute::OPTION_DEFAULT_VALUE => [ 'email@example.com' => '1234567890' ]
        ]);

        $attribute->getDefaultValue();
    }

    public function testThrowsOnInvalidEmailDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);

        $attribute = new EmailListAttribute('emailinvaliddefaultvalue', [
            EmailListAttribute::OPTION_DEFAULT_VALUE => [ 'emailexample.com' => '1234567890' ]
        ]);

        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new EmailListAttribute('emails');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [null],
            [false],
            [true],
            [1],
            ['' => 'asdf']
        ];
    }
}
