<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Module\IModule;
use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

/**
 * FieldMap is a associative collection container, that maps fieldnames to correspondig field instances.
 * As fields must be unique by name, it is not recommended using this class outside of a module's scope.
 */
class FieldMap extends TypedMap implements IUniqueCollection
{
    protected $module;

    public function __construct(IModule $module, array $items = array())
    {
        $this->module = $module;

        parent::__construct($items);
    }

    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);

        $value->setModule($this->module);
    }

    /**
     * Returns the IField interface-name to the TypeMap parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Field\\IField';
    }
}
