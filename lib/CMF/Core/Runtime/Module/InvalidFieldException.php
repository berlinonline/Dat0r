<?php

namespace CMF\Core\Runtime\Module;

use CMF\Core\Runtime;
use CMF\Core\Runtime\Error;

/**
 * InvalidFieldException(s) reflect attempts to access non-defined/private fields of a module.
 */
class InvalidFieldException extends Error\Exception
{
        
}
