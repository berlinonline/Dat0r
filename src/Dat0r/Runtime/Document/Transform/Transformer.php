<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;

class Transformer extends Object implements ITransformer
{
    protected $field_specification_set;

    public function getFieldSpecificationSet()
    {
        return $this->field_specification_set;
    }

    public function transform(IDocument $document)
    {
        // @todo transform stuff based on fieldset information
    }
}
