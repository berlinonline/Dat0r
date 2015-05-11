<?php

namespace Dat0r\Runtime;

interface ReferencedEntityTypeInterface
{
    public function getReferencedAttributeName();

    public function getReferencedTypeClass();
}
