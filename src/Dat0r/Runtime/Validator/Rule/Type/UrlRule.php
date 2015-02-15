<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

// @todo do some spoofchecking on the host part?
// @see http://stackoverflow.com/questions/17458876/php-spoofchecker-class
class UrlRule extends Rule
{
    const OPTION_DEFAULT_SCHEME = 'default_scheme';
    const OPTION_ADD_DEFAULT_SCHEME_IF_MISSING = 'add_default_scheme_if_missing';
    const OPTION_CONVERT_HOST_TO_IDN = 'convert_host_to_idn';
    const OPTION_ALLOWED_SCHEMES = 'allowed_schemes';

    const OPTION_HOST_ONLY = 'host_only';
    const OPTION_STRIP_PATH_QUERY_FRAGMENT = 'strip_path_query_fragment';
    const OPTION_ALLOW_PROTOCOL_RELATIVE_URL = 'allow_protocol_relative_url';

    // add options to:
    // - FORCE parts
    // - SET parts IF MISSING
    // - STRIP parts

    // - do confusable check on host part

    public function __construct($name, array $options = [])
    {
        // use sensible default max length for URLs
        if (!array_key_exists(TextRule::OPTION_MAX, $options)) {
            // http://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers
            $options[TextRule::OPTION_MAX] = 2048;
        }

        if (!array_key_exists(TextRule::OPTION_MIN, $options)) {
            $options[TextRule::OPTION_MIN] = 4;
        }

        if (!array_key_exists(TextRule::OPTION_REJECT_INVALID_UTF8, $options)) {
            $options[TextRule::OPTION_REJECT_INVALID_UTF8] = true;
        }

        if (!array_key_exists(TextRule::OPTION_TRIM, $options)) {
            $options[TextRule::OPTION_TRIM] = true;
        }

        if (!array_key_exists(TextRule::OPTION_STRIP_CONTROL_CHARACTERS, $options)) {
            $options[TextRule::OPTION_STRIP_CONTROL_CHARACTERS] = true;
        }

        if (!array_key_exists(TextRule::OPTION_ALLOW_CRLF, $options)) {
            $options[TextRule::OPTION_ALLOW_CRLF] = false;
        }

        if (!array_key_exists(TextRule::OPTION_ALLOW_TAB, $options)) {
            $options[TextRule::OPTION_ALLOW_TAB] = false;
        }

        parent::__construct($name, $options);
    }

    protected function execute($value)
    {
        if (!is_scalar($value)) {
            $this->throwError('non_scalar_value', [ 'value' => $value ], IncidentInterface::CRITICAL);
            return false;
        }

        $value = (string)$value;

        $text_rule = new TextRule('text', $this->getOptions());
        $is_valid = $text_rule->apply($value);
        if (!$is_valid) {
            foreach ($text_rule->getIncidents() as $incident) {
                $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
            }
            return false;
        }

        // we now have a valid string, that might be some kind of URL
        $val = $text_rule->getSanitizedValue();

        // FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED |
        // FILTER_FLAG_PATH_REQUIRED | FILTER_FLAG_QUERY_REQUIRED
        $url = filter_var($val, FILTER_VALIDATE_URL);
        if ($url === false) {
            $this->throwError('invalid_format', [ 'value' => $val ]);
            return false;
        }

        $this->setSanitizedValue($url);

        return true;


        $components = parse_url($value);
        $url_parts = $components;

        $default_scheme = 'http';
        if (!array_key_exists('scheme', $parts) && $add_default_scheme_if_missing) {
            $url_parts['scheme'] = $default_scheme;
        }

        if (!array_key_exists('host', $parts)) {
            $this->throwError('host_missing');
            return false;
        }

        if (!function_exists('idn_to_ascii')) {
            throw new RuntimeException(
                'The INTL extension needs to be installed to check international domain names of URLs.'
            );
        }

        $idn_host = idn_to_ascii($parts['host']); // @TODO options, variants, idna_info
        if ($idn_host === false) {
            $this->throwError('invalid_host');
        }

        $convert_host_to_idn = false;
        if ($convert_host_to_idn) {
            $url_parts['host'] = $idn_host;
        }


        // host
        // port
        // user
        // pass
        // path
        // query
        // fragment
/*
testcases:

$url = "http://스타벅스코리아.com";
var_dump(parse_url($url));
var_dump(idn_to_ascii("cåsino.com"));
var_dump(idn_to_ascii("cåsino"));
var_dump(idn_to_ascii("täst.de"));
var_dump(idn_to_ascii("müller.de"));
var_dump(idn_to_ascii("académie-française.fr")); // http://www.xn--acadmie-franaise-npb1a.fr
var_dump(idn_to_ascii("2001:0db8:0000:85a3:0000:0000:ac1f:8001"));
var_dump(idn_to_ascii('президент.рф'));
*/

    }
}
