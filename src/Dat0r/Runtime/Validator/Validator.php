<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Runtime\Field\IField;

/**
 * Base implementation of the IValidator interface.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 *
 * @todo Will probally be responseable for implementing validation reports
 * and other validation related basic functionalities.
 */
abstract class Validator implements IValidator
{
    /**
     * Holds th validator's associated field.
     *
     * @var IField $field
     */
    private $field;

    /**
     * Creates a new Validator instance for a given field.
     *
     * @param IField $field
     *
     * @return IValidator
     */
    public static function create(IField $field)
    {
        return new static($field);
    }

    /**
     * Constructs a new validator instance for the given field.
     *
     * @param IField $field
     */
    protected function __construct(IField $field)
    {
        $this->field = $field;
    }

    protected function getField()
    {
        return $this->field;
    }
}
