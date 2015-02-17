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

    public function testByDefaultInvalidUtf8IsRejected()
    {
        $rule = new UrlRule('url', []);
        $valid = $rule->apply("http://foo\xefbar.de");
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testStripInvalidUtf8IfWanted()
    {
        $rule = new UrlRule('url', [ 'reject_invalid_utf8' => false ]);
        $valid = $rule->apply("http://foo\xefbar.de/");
        $this->assertTrue($valid);
        $this->assertEquals("http://foobar.de/", $rule->getSanitizedValue());
    }

    public function testByDefaultControlCharactersAreRemovedAndTextIsTrimmed()
    {
        $rule = new UrlRule('url', []);
        $valid = $rule->apply("     http://foo\x00\t\r\nbar.de ");
        $this->assertEquals("http://foobar.de", $rule->getSanitizedValue());
    }

    public function testPunycodeConversion()
    {
        $rule = new UrlRule('url', [ 'convert_host_to_punycode' => true]);
        $valid = $rule->apply("   http://www.académie-française.fr ");
        $this->assertEquals("http://www.xn--acadmie-franaise-npb1a.fr", $rule->getSanitizedValue());
    }

    public function testRequirePort()
    {
        $rule = new UrlRule('url', [ 'require_port' => true ]);
        $valid = $rule->apply("http://foobar.de ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForcePort()
    {
        $rule = new UrlRule('url', [ 'force_port' => 443 ]);
        $valid = $rule->apply("https://foobar.de:80 ");
        $this->assertEquals("https://foobar.de:443", $rule->getSanitizedValue());
    }

    public function testDefaultPort()
    {
        $rule = new UrlRule('url', [ 'default_port' => 443 ]);
        $valid = $rule->apply("https://foobar.de ");
        $this->assertEquals("https://foobar.de:443", $rule->getSanitizedValue());
    }

    public function testRequireUser()
    {
        $rule = new UrlRule('url', [ 'require_user' => true ]);
        $valid = $rule->apply("http://foobar.de ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceUser()
    {
        $rule = new UrlRule('url', [ 'force_user' => 'asdf' ]);
        $valid = $rule->apply("https://qwer@foobar.de:80 ");
        $this->assertEquals("https://asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testDefaultUser()
    {
        $rule = new UrlRule('url', [ 'default_user' => 'asdf' ]);
        $valid = $rule->apply("https://foobar.de:80 ");
        $this->assertEquals("https://asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testRequirePass()
    {
        $rule = new UrlRule('url', [ 'require_pass' => true ]);
        $valid = $rule->apply("http://asdf@foobar.de ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceUserPass()
    {
        $rule = new UrlRule('url', [ 'force_pass' => 'asdf' ]);
        $valid = $rule->apply("https://foo:bar@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testDefaultUserPass()
    {
        $rule = new UrlRule('url', [ 'default_pass' => 'asdf' ]);
        $valid = $rule->apply("https://foo@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testRequirePath()
    {
        $rule = new UrlRule('url', [ 'require_path' => true ]);
        $valid = $rule->apply("http://asdf@foobar.de?asdf ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForcePath()
    {
        $rule = new UrlRule('url', [ 'force_path' => '/foo/bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/foo/bar", $rule->getSanitizedValue());
    }

    public function testDefaultPath()
    {
        $rule = new UrlRule('url', [ 'default_path' => '/foo/bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80?asdf ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/foo/bar?asdf", $rule->getSanitizedValue());
    }

    public function testRequireQuery()
    {
        $rule = new UrlRule('url', [ 'require_query' => true ]);
        $valid = $rule->apply("http://asdf@foobar.de/asdf ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceQuery()
    {
        $rule = new UrlRule('url', [ 'force_query' => 'foo=bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80/?blah ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/?foo=bar", $rule->getSanitizedValue());
    }

    public function testDefaultQuery()
    {
        $rule = new UrlRule('url', [ 'default_query' => 'foo=bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/?foo=bar", $rule->getSanitizedValue());
    }

    public function testRequireFragment()
    {
        $rule = new UrlRule('url', [ 'require_fragment' => true ]);
        $valid = $rule->apply("http://asdf@foobar.de/asdf?asdf ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceFragment()
    {
        $rule = new UrlRule('url', [ 'force_fragment' => 'foobar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/#foobar", $rule->getSanitizedValue());

        $rule = new UrlRule('url', [ 'force_fragment' => 'foobar' ]);
        $valid = $rule->apply("https://foobar.de/blah/blub#asdf");
        $this->assertEquals("https://foobar.de/blah/blub#foobar", $rule->getSanitizedValue());
    }

    public function testDefaultFragment()
    {
        $rule = new UrlRule('url', [ 'default_fragment' => 'foobar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80/blub?blah ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/blub?blah#foobar", $rule->getSanitizedValue());
    }

    public function testDefaultScheme()
    {
        $rule = new UrlRule('url', [ 'default_scheme' => 'http' ]);
        $valid = $rule->apply("asdf.com:80/blub?blah ");
        $this->assertEquals("http://asdf.com:80/blub?blah", $rule->getSanitizedValue());
    }

    public function testAllowedSchemesFails()
    {
        $rule = new UrlRule('url', [ 'allowed_schemes' => ['http', 'https'] ]);
        $valid = $rule->apply("ftp://user:pass@asdf.com:21/blub?blah ");
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testAllowedSchemesFtp()
    {
        $rule = new UrlRule('url', [ 'allowed_schemes' => ['ftp'] ]);
        $valid = $rule->apply("ftp://user:pass@asdf.com:21/blub?blah ");
        $this->assertTrue($valid);
        $this->assertEquals("ftp://user:pass@asdf.com:21/blub?blah", $rule->getSanitizedValue());
    }

    public function testDomainSpoofcheckingAutomaticallyConvertsToPunycode()
    {
        // see http://en.wikipedia.org/wiki/IDN_homograph_attack
        $rule = new UrlRule('url', []);
        $cyrillic_domain = "http://wіkіреdіа.org"; // as punycode: xn--http://wkd-8qi2d4hsmbd.org
        $valid = $rule->apply($cyrillic_domain); // contains cyrillic characters instead of simple ascii ones!
        // the domain as punycode is valid, but contains characters from multiple character sets and is thus converted
        $this->assertTrue($valid);
        $this->assertEquals('http://xn--wkd-8cdx9d7hbd.org', $rule->getSanitizedValue());
    }

    public function testRejectSuspiciousHost()
    {
        $rule = new UrlRule('url', [ 'accept_suspicious_host' => false ]);
        $cyrillic_domain = "http://wіkіреdіа.org"; // as punycode: xn--http://wkd-8qi2d4hsmbd.org
        $valid = $rule->apply($cyrillic_domain);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
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
            array("https://kosme.gr/path?q=1&foo=bar#baz", 'kosme as hostname'),
            array("HTTPS://www.spiegel.de", 'HTTPS://www.spiegel.de'),
            array("http://localhost/test/de/asdf", 'http://localhost/test/de/asdf'),
            array("http://test.sub.domain.domain.com:8080/test", "http://test.sub.domain.domain.com:8080/test"),
            array("http://test-sub-domain.domain.com:8080/test", "http://test-sub-domain.domain.com:8080/test"),
        );
    }

    /**
     * @dataProvider provideValidIdnUrls
     */
    public function testValidIdnUrl($valid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertTrue(
            $rule->getSanitizedValue() === $valid_url,
            $assert_message . ' should be set as sanitized url'
        );
    }

    /**
     * @dataProvider provideValidIdnUrls
     */
    public function testValidPunycodeUrl($valid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [ 'convert_host_to_punycode' => true ]);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertTrue(
            $rule->getSanitizedValue() !== $valid_url,
            $assert_message . ' should be set as sanitized url'
        );
    }

    public function provideValidIdnUrls()
    {
        return array(
            array("https://κόσμε.gr/path?q=1&foo=bar#baz", 'greek word "kosme" as hostname'),
            array("http://스타벅스코리아.com", 'http://스타벅스코리아.com'),
            array("http://académie-française.fr", 'académie-française.fr'),
            array("http://президент.рф", 'президент.рф'),
        );
    }

    /**
     * @dataProvider provideSuspiciousUrls
     */
    public function testValidSuspicousUrl($valid_url, $valid_punycode_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [ ]);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertEquals(
            $rule->getSanitizedValue(),
            $valid_punycode_url,
            $assert_message . ' as punycode should be set as sanitized url'
        );
    }

    public function provideSuspiciousUrls()
    {
        return array(
            array("http://Рaypal.com", "http://xn--aypal-uye.com", "Рaypal.com"),
            array("http://cåsino.com", 'http://xn--csino-mra.com', 'cåsino.com'),
            array("http://täst.de", 'http://xn--tst-qla.de', 'täst.de'),
            array("http://müller.de", 'http://xn--mller-kva.de', 'müller.de'),
        );
    }

    /**
     * @dataProvider provideValidIpv6Urls
     */
    public function testValidIpv6Url($valid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid ipv6 url');
        $this->assertTrue(
            $rule->getSanitizedValue() === $valid_url,
            $assert_message . ' should be set as sanitized url'
        );
    }

    public function provideValidIpv6Urls()
    {
        return array(
            array("http://[2001:0db8:0000:85a3:0000:0000:ac1f:8001]/foo", 'ipv6 w/ path'),
            array("http://[2620:0:2d0:200::10]/foo?bar", "ipv6 w/ brackets, path and query"),
            array("http://[2001:db8:0:85a3:0:0:ac1f:8001]:123/me.html", "ipv6 with brackets should be valid in URLs"),
            array("http://[fe80:0000:0000:0000:0204:61ff:fe9d:f156]/foo", 'normal ipv6 address/foo'),
            array("http://[fe80:0:0:0:204:61ff:fe9d:f156]/foo", 'normal ipv6 address w/o leading zeros /foo'),
            array("http://[fe80::204:61ff:fe9d:f156]/foo", 'normal compressed ipv6 address /foo'),
            array("http://[fe80:0000:0000:0000:0204:61ff:254.157.241.86]/foo", 'ipv6/v4 mix 1'),
            array("http://[fe80:0:0:0:0204:61ff:254.157.241.86]/foo", 'ipv6/v4 mix 2'),
            array("http://[fe80::204:61ff:254.157.241.86]/foo", 'ipv6/v4 mix 3'),
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
            array("http://\xfe\x00\x00\n\r\t\n", 'scheme only with invalid utf8 and control chars'),
            array("http://\x00\x00\n\r\t\n\t\n\t\n\t\t\n", 'scheme only with null bytes and control chars'),
            array("les-tilleuls.coop:8080test", 'les-tilleuls.coop:8080test'),
            array(
                "http://test_sub_domain.domain.com:8080/test",
                "Underscore _ is valid in URIs but not in URLs or HOST headers"
            ),
            array("http://testsub+domain.domain.com:8080/test", "Plus sign + is not valid in URLs"),
            array("http://testsub~domain.domain.com:8080/test", "Tilde ~ is valid in URIs but not in URLs"),
            array("http:les-tilleuls.coop", 'http:les-tilleuls.coop'),
            array(
                "http://toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong.com",
                'too long domain'
            ),
            array("[a:b:c:z:e:f:]", '[a:b:c:z:e:f:]'),
            array("http://::1/foo", 'http://::1/foo'),
            array("http://..com", 'no domain label'),
            // the following will hopefully be wrong in more recent versions of PHP's FILTER_VALIDATE_URL
            //array("http://a.-bc.com", 'leading - in domain label'),
            //array("http://ab.cd-.com", 'trailing - in domain label'),
            //array("http://abc.-.abc.com", 'domain label "-"'),
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
