<?php

namespace Dat0r\Generic;

interface IObject
{
    public static function create(array $data = array());

    public function toArray();
}
