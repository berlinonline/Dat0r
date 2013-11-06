<?php

namespace Dat0r\Runtime\Module;

use Dat0r\Runtime\Error;

/**
 * InvalidFieldException(s) reflect attempts to access non-defined/private fields of a module.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class InvalidFieldException extends Error\Exception
{
}
