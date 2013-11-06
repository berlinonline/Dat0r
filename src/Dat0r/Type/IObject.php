<?php

namespace Dat0r\Type;

interface IObject
{
    public static function create(array $data = array());

    public function toArray();
}
