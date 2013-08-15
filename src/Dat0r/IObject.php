<?php

namespace Dat0r;

interface IObject
{
    public static function create(array $data = array());

    public function toArray();
}
