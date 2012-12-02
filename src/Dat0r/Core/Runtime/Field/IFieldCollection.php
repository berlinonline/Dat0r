<?php

namespace Dat0r\Core\Runtime\Field;

use Dat0r\Core\Runtime\IFreezable;

/**
 * IFieldCollection represents a list of IField.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IFieldCollection extends \Iterator, IFreezable, \Countable, \ArrayAccess 
{
    /**
     * Creates a new field collection passing in an initial set of fields.
     *
     * @param array $fields
     *
     * @return FieldCollection
     */
    public static function create(array $fields = array());

    /**
     * Adds a field to the collection.
     *
     * @param IField $field 
     */
    public function add(IField $field);

    /**
     * Adds several fields to the collection at once.
     *
     * @param array $fields
     */
    public function addMore(array $fields);

    /**
     * Tells if a given field is allready inside the collection.
     * 
     * @param IField $field
     *
     * @return boolean
     */
    public function has(IField $field);

    /**
     * Returns a field from the collection searching it by name.
     *
     * @param string $name
     *
     * @return IField
     */
    public function get($name);

    /**
     * Returns an array representation of the current collection instance.
     *
     * @return array
     */
    public function toArray();
}
