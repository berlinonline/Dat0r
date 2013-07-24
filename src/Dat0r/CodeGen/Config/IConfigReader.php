<?php

namespace Dat0r\CodeGen\Config;

use Dat0r;

interface IConfigReader
{
    public function read($config_source);
}
