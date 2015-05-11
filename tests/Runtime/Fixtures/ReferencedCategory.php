<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Textarea\TextareaAttribute;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\Entity\Entity;
use Dat0r\Runtime\Entity\EntityReferenceInterface;

class ReferencedCategory extends Entity implements EntityReferenceInterface
{
    public function getIdentifier()
    {
        return $this->getValue('identifier');
    }

    public function getReferencedIdentifier()
    {
        return $this->getValue('referenced_identifier');
    }
}
