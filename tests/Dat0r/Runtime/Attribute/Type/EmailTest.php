<?php

namespace Dat0r\Tests\Runtime\Attribute\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Type\Email;
use Dat0r\Runtime\Validator\Result\IIncident;
use stdClass;

/**
 * @todo All commented-out fixtures within the dataProvider methods,
 * must be checked to see if
 * 1. either our expectations are not correct
 * 2. the email validator component we are using needs some help
 *
 * @see http://isemail.info/ for debugging this stuff
 */
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

    /**
     * @dataProvider provideValidEmails
     */
    public function testValidEmail($valid_email, $assert_message = '')
    {
        $email_attribute = new Email('email');
        $result = $email_attribute->getValidator()->validate($valid_email);
        $this->assertEquals(IIncident::SUCCESS, $result->getSeverity(), $assert_message);
    }

    /**
     * @dataProvider provideInvalidEmails
     */
    public function testInvalidEmail($invalid_email, $assert_message = '')
    {
        $email_attribute = new Email('email');
        $result = $email_attribute->getValidator()->validate($invalid_email);
        $this->assertEquals(IIncident::ERROR, $result->getSeverity(), $assert_message);
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $email_attribute = new Email('email');
        $result = $email_attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IIncident::CRITICAL, $result->getSeverity(), $assert_message);
    }

    public function provideValidEmails()
    {
        return array(
            array('user@example.com'),
            array('user+folder@example.com'),
            array('someone@example.business'),
            array('new-asdf@trololo.co.uk'),
            array('omg@nsfw.xxx'),
            array(
                'A-Za-z0-9.!#$%&*+-/=?^_`{|}~@example.com',
                'A lot of special characters should be valid in the local part of email addresses'
            ),
            array(
                "o'hare@example.com",
                'Single quotes are not working'
            ),
            array(
                "o'hare@xn--mller-kva.example",
                'International domains should be supported via Punycode ACE strings'
            ),
            array(
                'user@example123example123example123example123example123example123456.com',
                '63 characters long domain names should be valid'
            ),
            array(
                'user@example123example123example123example123example123example123456.co.nz',
                '63 characters long domain names with top level domain "co.nz" should be valid'
            ),
            array(
                'example123example123example123example123example123example1234567@example.com',
                '64 characters are valid according to SMTP in the local part'
            ),
            array(
                '"Someone other" <someone@example.com>',
                'Quoted display names with email addresses may be valid, but are not support by us'
            ),
            array('user@localhost'),
            // This one should be supported but isn't at the moment
            // array(
            //    '"foo bar"@example.com',
            //    'Spaces in email addresses should be allowed when they are in double quotes'
            // ),
        );
        // @todo add other tests for length constraints
        // - 320 octets overall, 64 for local part according to SMTP, 254 chars overall if you combine RFCs etc.
    }

    public function provideInvalidEmails()
    {
        return array(
            // array(
            //     'müller@example.com',
            //     'Umlauts in the local part are not allowed'
            // ),
            // array(
            //     'umlaut@müller.com',
            //     'Umlauts etc. in the domain part should only be accepted punycode encoded'
            // ),
            array('trololo'),
            array('@'),
            array('<foo>@example.com'),
            // array('a@b'),
            array(
                '<foo>@example.com',
                'Characters < and > should not be not valid in email addresses'
            ),
            array(
                'Someone other <someone@example.com>',
                 'Display names with email addresses may be valid, but are not support by us'
            ),
            // array(
            //     'user@example123example123example123example123example123example1234567.com',
            //     'Domain names longer than 63 characters are invalid'
            // ),
            // array(
            //     'user@' . str_repeat('example123', 20) . '@' . str_repeat('example123', 20) . '.com',
            //     '320 octets/bytes are the maximum allowed length according to RFC 5322 and RFC 5321 valid emails'
            // ),
        );
    }

    public function provideInvalidValues()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(array()),
            array(new stdClass()),
            array(1)
        );
    }
}
