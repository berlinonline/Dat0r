<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Options;

class FieldSpecSet extends Object implements IFieldSpecSet
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var Options $options
     */
    protected $options;

    /**
     * @var FieldSpecMap $field_specs
     */
    protected $field_specs;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return FieldSpecMap
     */
    public function getFieldSpecs()
    {
        return $this->field_specs;
    }

    /**
     * @param mixed $field_specs Either 'FieldSpecMap' instance or array suitable for creating one.
     */
    protected function setFieldSpecs($field_specs)
    {
        if ($field_specs instanceof FieldSpecMap) {
            $this->field_specs = $field_specs;
        } else if (is_array($field_specs)) {
            $this->field_specs = FieldSpecMap::create();
            foreach ($field_specs as $spec_key => $field_spec) {
                if ($field_spec instanceof IFieldSpec) {
                    $this->field_specs->setItem($spec_key, $field_spec);
                } else {
                    $this->field_specs->setItem($spec_key, FieldSpec::create($field_spec));
                }
            }
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'FieldSpecMap' and 'array' are supported."
            );
        }
    }

    /**
     * @param mixed $options Either 'Options' instance or array suitable for creating one.
     */
    protected function setOptions($options)
    {
        if ($options instanceof Options) {
            $this->options = $options;
        } else if (is_array($options)) {
            $this->options = new Options($options);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'Options' and 'array' are supported."
            );
        }
    }
}
