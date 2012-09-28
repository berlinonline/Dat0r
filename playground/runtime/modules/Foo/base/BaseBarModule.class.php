<?php
/*              AUTOGENERATED CODE - DO NOT EDIT !
This base class was generated by the Midas Content Management Framework
on 2012-09-28 13:52:05 and is closed to modifications by any meaning.
If you are looking for a place to alter the behaviour of the 'Bar' module,
then the 'BarModule' (skeleton) class probally might be a good place to look. */

namespace CMF\Runtime\Domain\Foo;

/**
 * Serves as the base class to the 'Bar'' module skeleton.
 */
abstract class BaseBarModule extends \CMF\Core\Runtime\Module\AggregateModule
{
    /**
     * Creates a new BaseBarModule instance.
     */
    protected function __construct()
    {
        return parent::__construct('Bar', array( 
            \CMF\Core\Runtime\Field\TextField::create('food', array(                                 
                'pizza' => array('toppings' => array('cheese', 'pepperonis', ),),  
            )), 
            \CMF\Core\Runtime\Field\IntegerField::create('clickCount'),         
        ));
    }

    /**
     * Returns the IDocument implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an IDocument implementation.
     */
    protected function getDocumentImplementor()
    {
        return 'CMF\Runtime\Domain\Foo\BarDocument';
    }
}
