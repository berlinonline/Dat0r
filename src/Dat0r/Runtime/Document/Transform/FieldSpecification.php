<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;

class FieldSpecification extends Object implements IFieldSpecification
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
     * @param mixed $options Either 'Options' instance or array suitable for creating one.
     */
    protected function setOptions($options)
    {
        /*
        if ($options instanceof Options) {
            $this->options = $options;
        } else if (is_array($options)) {
            // $this->options = new Options($options);
        } else {
            throw new BadValueException(
                "Invalid argument given. Only the types 'FieldSpecificationMap' and 'array' are supported."
            );
        }
        */
    }
}
