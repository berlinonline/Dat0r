<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema\EntityTypeDefinitionList;
use Dat0r\CodeGen\Schema\EmbedDefinition;

interface ParserInterface
{
    /**
     * Parses the given source and returns the result.
     *
     * @return mixed
     */
    public function parse($source, array $options = []);
}
