<?php

namespace Dat0r\Common;

class Configurable extends Object
{
    /**
     * @var Options $options
     */
    protected $options;

    /**
     * Override the Object's create method, in order to make sure we always initialize our options.
     *
     * @param array $state An array with property names as keys and property values as array values.
     *
     * @return IObject
     */
    public static function create(array $state = array())
    {
        $configurable = parent::create($state);

        if (!$configurable->getOptions()) {
            $configurable->options = new Options();
        }

        return $configurable;
    }

    /**
     * Return this object's options instance.
     *
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the option value for the given option name.
     *
     * @param string $name name of option
     * @param mixed $default value to return if option doesn't exist
     *
     * @return mixed value for that option or default given
     */
    public function getOption($name, $default = null)
    {
        return $this->options->get($name, $default);
    }

    /**
     * Returns whether the option exists or not.
     *
     * @param string $name Name of the option to check
     *
     * @return bool true, if option exists; false otherwise
     */
    public function hasOption($name)
    {
        return $this->options->has($name);
    }

    /**
     * Sets a given value for the specified option.
     *
     * @param string $name name of the option
     * @param mixed $value value to set for the given option name
     *
     * @return mixed the value set
     */
    public function setOption($name, $value)
    {
        return $this->options->set($name, $value);
    }

    /**
     * Delete all options.
     */
    public function clearOptions()
    {
        $this->options->clear();
    }

    /**
     * Set an object's options.
     *
     * @param mixed $options Either 'Options' instance or array suitable for creating one.
     */
    public function setOptions($options)
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
