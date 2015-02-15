<?php

namespace Dat0r\Tests\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Type\UrlRule;
use Dat0r\Tests\TestCase;
use stdClass;

class UrlRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new UrlRule('url', []);
        $this->assertEquals('url', $rule->getName());
    }

    public function testByDefaultControlCharactersAreRemovedAndTextIsTrimmed()
    {
        $rule = new UrlRule('url', [ 'reject_invalid_utf8' => false ]);
        $valid = $rule->apply("     http://foo\x00\t\r\nbar.de ");
        $this->assertEquals("http://foobar.de", $rule->getSanitizedValue());
    }

    /**
     * @dataProvider provideValidUrls
     */
    public function testValidUrl($valid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertTrue(
            $rule->getSanitizedValue() === $valid_url,
            $assert_message . ' should be set as sanitized url'
        );
    }

    public function provideValidUrls()
    {
        return array(
            array("http://heise.de", 'http://heise.de'),
            array("https://kosme.gr/path?q=1&foo[]=bar#baz", 'kosme as hostname'),
            //array("https://κόσμε.gr/path?q=1&foo[]=bar#baz", 'greek word "kosme" as hostname'),
            array("HTTPS://www.spiegel.de", 'HTTPS://www.spiegel.de'),
        );
    }

    /**
     * @dataProvider provideIllformedUrls
     */
    public function testIllformedUrl($invalid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [ 'min' => 8 ]);
        $this->assertFalse($rule->apply($invalid_url), $assert_message . ' should be an invalid url');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized url');
    }

    public function provideIllformedUrls()
    {
        return array(
            //array('localhost', 'localhost'),
            array("http://\xfe\x00\x00\n\r\t\n", 'scheme only with invalid chars'),
        );
    }

    /**
     * @dataProvider provideInvalidUrls
     */
    public function testInvalidUrl($invalid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);
        $this->assertFalse($rule->apply($invalid_url), $assert_message . ' should be an invalid url');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized url');
    }

    public function provideInvalidUrls()
    {
        return array(
            array(null, 'NULL'),
            array(false, 'FALSE'),
            array(true, 'TRUE'),
            array(new stdClass(), 'stdClass object'),
        );
    }
}
