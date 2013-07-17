<?php

namespace Dat0r\Core\Field;

use Dat0r\Core\ValueHolder\TextValueHolder;

/**
 * Concrete implementation of the Field base class.
 * Stuff in here is dedicated to handling text.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class UuidField extends TextField
{
    /**
     * Returns the default value of the field.
     *
     * @return IValueHolder
     */
    public function getDefaultValue()
    {
        return $this->generateUuidV4();
    }

    // from http://www.php.net/manual/en/function.uniqid.php#94959
    // more info http://en.wikipedia.org/wiki/Universally_unique_identifier#Version_4_.28random.29
    protected function generateUuidV4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
