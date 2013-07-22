<?php
/*              AUTOGENERATED CODE - DO NOT EDIT !
This base class was generated by the Dat0r library (https://github.com/berlinonline/Dat0r)
on 2013-07-22 22:43:39 and is closed to modifications by any meaning.
If you are looking for a place to alter the behaviour of the 'SimpleSchema' module,
then the 'SimpleSchemaModule' (skeleton) class probally might be a good place to look. */

namespace Example\Domain\SimpleSchema\Base;

use Dat0r\Core\Module;

/**
 * Serves as the base class to the 'SimpleSchema'' module skeleton.
 */
abstract class SimpleSchemaModule extends Module\RootModule
{
    /**
     * Creates a new SimpleSchemaModule instance.
     */
    protected function __construct()
    {
        parent::__construct('SimpleSchema', array( 
            Dat0r\Core\Field\TextField::create('title', array('mandatory' => 'true',)), 
            Dat0r\Core\Field\TextareaField::create('content', array('use_richtext' => 'yes',)), 
            Dat0r\Core\Field\TextCollectionField::create('keywords'), 
            Dat0r\Core\Field\AggregateField::create('votingStats', array('modules' => array('VotingStats', ),)), 
        ), array('some_option' => 'with_some_value',) );
    }

    /**
     * Returns the IDocument implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an IDocument implementation.
     */
    protected function getDocumentImplementor()
    {
        return 'Example\Domain\SimpleSchema\SimpleSchemaDocument';
    }
}
