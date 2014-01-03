<?php

namespace Dat0r\Common;

interface IObject
{
    public static function create(array $state = array());

    public function toArray();
}
