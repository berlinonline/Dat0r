<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;
use Dat0r\Common\Options;

interface ISpecification
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return Options
     */
    public function getOptions();

    /**
     * Returns the option value for the given option name.
     *
     * @param string $name name of option
     * @param mixed $default value to return if option doesn't exist
     *
     * @return mixed value for that option or default given
     */
    public function getOption($name, $default = null);

    /**
     * Returns whether the option exists or not.
     *
     * @param string $name Name of the option to check
     *
     * @return bool true, if option exists; false otherwise
     */
    public function hasOption($name);

    /**
     * Sets a given value for the specified option.
     *
     * @param string $name name of the option
     * @param mixed $value value to set for the given option name
     *
     * @return mixed the value set
     */
    public function setOption($name, $value);

    /**
     * Delete all options.
     */
    public function clearOptions();

    /**
     * Set an object's options.
     *
     * @param mixed $options Either 'Options' instance or array suitable for creating one.
     */
    public function setOptions($options);
}
