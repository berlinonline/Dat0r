<?php

namespace Dat0r\Common;

interface IObject
{
    public static function create(array $data = array());

    public function toArray();
}
