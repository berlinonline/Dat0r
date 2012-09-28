<?php

namespace CMF\Core\Runtime\Validator;

use CMF\Core\Runtime\Field;

/**
 * Base implementation of the IValidator interface.
 * @todo Will probally be responseable for implementing validation reports 
 * and other validation related basic functionalities.
 */
abstract class Validator implements IValidator
{
    /**
     * @var CMF\Core\Runtime\Field\IField $field
     */
    private $field;

    /**
     * Creates a new Validator instance for a given field.
     *
     * @param CMF\Core\Runtime\Field\IField $field
     *
     * @return CMF\Core\Runtime\Validator\IValidator
     */     
    public static function create(Field\IField $field)
    {
        return new static($field);
    }

    /**
     * Constructs a new validator instance for the given field.
     */
    protected function __construct(Field\IField $field)
    {
        $this->field = $field;
    }
}
