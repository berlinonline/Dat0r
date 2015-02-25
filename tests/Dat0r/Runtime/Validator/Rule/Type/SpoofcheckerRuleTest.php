<?php

namespace Dat0r\Tests\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Type\SpoofcheckerRule;
use Dat0r\Tests\TestCase;
use stdClass;

class SpoofcheckerRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new SpoofcheckerRule('url', []);
        $this->assertEquals('url', $rule->getName());
    }
/*
    public function testZeroWidthSpace()
    {
        $rule = new SpoofcheckerRule('url', []);
        $zws = "some\xE2\x80\x8Btext";
        $value = str_replace("\xE2\x80\x8B", "", $zws);
        // $value = preg_replace("/\xE2\x80\x8B/", "", $zws);
        $value = "sometext";
        var_dump('VALUE=', $value);
        $valid = $rule->apply($value);
        $this->assertTrue($valid);
    }
 */
    public function testAllowedEnUsLocaleRejectsKorean()
    {
        $rule = new SpoofcheckerRule('text', [
            'allowed_locales' => 'en_US'
        ]);
        $korean = "\xED\x95\x9C" . "\xEA\xB5\xAD" . "\xEB\xA7\x90";
        $valid = $rule->apply($korean);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testAllowedLocaleAcceptsKorean()
    {
        $rule = new SpoofcheckerRule('text', [
            'allowed_locales' => 'en_US, ko_KR'
        ]);
        $korean = "\xED\x95\x9C" . "\xEA\xB5\xAD" . "\xEB\xA7\x90";
        $valid = $rule->apply($korean);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    public function testAcceptKoreanByDefault()
    {
        $rule = new SpoofcheckerRule('text', []);
        $korean = "\xED\x95\x9C" . "\xEA\xB5\xAD" . "\xEB\xA7\x90";
        $valid = $rule->apply($korean);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    public function testRejectConfusableStrings()
    {
        $rule = new SpoofcheckerRule('text', [
            'visually_confusable_strings' => [
                'hello',
                'google',
            ]
        ]);

        $valid = $rule->apply("h\xD0\xB5llo");
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());

        $valid = $rule->apply("goog1e");
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }
/*
    public function testRejectZeroWidthSpaceAsSuspicious()
    {
        $rule = new SpoofcheckerRule('text', []);
        $zero_width_space = "some\xE2\x80\x8Btext";
        $valid = $rule->apply($zero_width_space);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }
 */
    public function testAcceptZeroWidthSpace()
    {
        $rule = new SpoofcheckerRule('text', [ 'accept_suspicious_strings' => true ]);
        $zero_width_space = "some\xE2\x80\x8Btext";
        $valid = $rule->apply($zero_width_space);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }
/*
    public function testRejectInvisibleSeparator()
    {
        $rule = new SpoofcheckerRule('text', []);
        $invsep = "some\xE2\x81\xA3text";
        $valid = $rule->apply($invsep);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }
 */
    public function testMixedScriptIsOkay()
    {
        $greek = "\xCE\x9F\xCE\xB4\xCF\x8C\xCF\x82"; // Οδός
        $lithuanian = "\xC3\x8F \xC3\x8D J J\xCC\x88"; // Ï Í J J̈
        $rule = new SpoofcheckerRule('text', [
        ]);
        $valid = $rule->apply($greek.$lithuanian);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    public function testEnforceSingleScript()
    {
        $greek = "\xCE\x9F\xCE\xB4\xCF\x8C\xCF\x82"; // Οδός
        $lithuanian = "\xC3\x8F \xC3\x8D J J\xCC\x88"; // Ï Í J J̈
        $rule = new SpoofcheckerRule('text', [
            'enforce_single_script' => true
        ]);
        $valid = $rule->apply($greek.$lithuanian);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testZeroWidthNonJoinerAllowed()
    {
        $zwnj_ar = "أي‌بي‌إم"; // arabic: IBM
        $zwnj = "foo\xE2\x80\x8Cbar";
        $rule = new SpoofcheckerRule('text', []);
        $valid = $rule->apply($zwnj_ar);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
        $valid = $rule->apply($zwnj);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    /**
     * @see http://www.fileformat.info/info/unicode/category/Mn/list.htm for other non-spacing marks
     */
    public function testNonSpacingMarkIsRejected()
    {
        $rule = new SpoofcheckerRule('text', [ ]);
        $combining_dot_above = "asdu\xCC\x87blah"; // confusable with "asdüblah"
        $valid = $rule->apply($combining_dot_above);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    /**
     * @see http://www.fileformat.info/info/unicode/category/Mn/list.htm for other non-spacing marks
     */
    public function testNonSpacingMarkCanBeAccepted()
    {
        $rule = new SpoofcheckerRule('text', [ 'accept_suspicious_strings' => true ]);
        $combining_dot_above = "asdu\xCC\x87blah"; // confusable with "asdüblah"
        $valid = $rule->apply($combining_dot_above);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    public function testAcceptSuspiciousStringIfWanted()
    {
        $rule = new SpoofcheckerRule('url', [ 'accept_suspicious_strings' => true ]);
        $cyrillic_domain = "http://wіkіреdіа.org";
        $valid = $rule->apply($cyrillic_domain);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    /**
     * @dataProvider provideSuspiciousTexts
     */
    public function testSuspicousText($text, $assert_message = '')
    {
        $rule = new SpoofcheckerRule('text', [ ]);

        $valid = $rule->apply($text);
        $this->assertFalse($valid, $assert_message . ' should be suspicious and thus not a valid text');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' must not be accepted as sanitized text');
    }

    public function provideSuspiciousTexts()
    {
        return array(
            array(
                "http://Рaypal.com",
                "suspicious Рaypal.com"
            ),
            array(
                "http://wіkіреdіа.org",
                "wikipedia with chars from belorussia etc."
            ),
            array(
                "http://www.payp\xD0\xB0l.com",
                "paypal with cyrillic spoof characters"
            ),
        );
    }

    /**
     * @dataProvider provideInvalidUrls
     */
    public function testInvalidUrl($invalid, $assert_message = '')
    {
        $rule = new SpoofcheckerRule('url', []);
        $this->assertFalse($rule->apply($invalid), $assert_message . ' should be invalid');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized value');
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
