<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Runtime\IDocumentType;
use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

/**
 * AttributeMap is a associative collection container, that maps attribute names to correspondig attribute instances.
 * As attributes must be unique by name, it is not recommended using this class outside of a type's scope.
 */
class AttributeMap extends TypedMap implements IUniqueCollection
{
    protected $type;

    public function __construct(IDocumentType $type, array $items = array())
    {
        $this->type = $type;

        parent::__construct($items);
    }

    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);

        $value->setType($this->type);
    }

    /**
     * Returns the IAttribute interface-name to the TypeMap parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\IAttribute';
    }
}
