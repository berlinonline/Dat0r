<?php

namespace Dat0r\CodeGen\Schema;

interface IDefinition
{
    public static function create(array $data = array());

    public function toArray();
}
