<?php

namespace Dat0r\Core\Runtime\Module;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Error;

/**
 * InvalidFieldException(s) reflect attempts to access non-defined/private fields of a module.
 */
class InvalidFieldException extends Error\Exception
{
        
}
