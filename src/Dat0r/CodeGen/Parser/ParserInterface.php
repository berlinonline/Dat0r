<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema\TypeDefinitionList;
use Dat0r\CodeGen\Schema\AggregateDefinition;

interface ParserInterface
{
    /**
     * Parses the given source and returns the result.
     *
     * @return mixed
     */
    public function parse($source, array $options = array());
}