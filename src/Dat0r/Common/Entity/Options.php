<?php

namespace Dat0r\Common\Entity;

use ArrayAccess;
use InvalidArgumentException;

/**
 * Class that wraps an associative array for convenience reasons.
 */
class Options extends Entity implements ArrayAccess
{
    /**
     * @var array with key => value pairs
     */
    protected $options = array();

    /**
     * Returns a new Options instance hydrated with the given initial options.
     *
     * @param array $options Initial options.
     *
     * @return Options
     */
    public static function create(array $options = array())
    {
        return new static($options);
    }

    /**
     * Create a new instance with the given options as initial value set.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Returns the value for the given key.
     *
     * @param string $key name of key
     * @param mixed $default value to return if key doesn't exist
     *
     * @return mixed value for that key or default given
     */
    public function get($key, $default = null)
    {
        if (isset($this->options[$key]) || array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return $default;
    }

    /**
     * Sets a given value for the specified key.
     *
     * @param string $key name of entry
     * @param mixed $value value to set for the given key
     *
     * @return mixed the value set
     */
    public function set($key, $value)
    {
        return $this->options[$key] = $value;
    }

    /**
     * Returns whether the key exists or not.
     *
     * @param string $key name of key to check
     *
     * @return bool true, if key exists; false otherwise
     */
    public function has($key)
    {
        if (isset($this->options[$key]) || array_key_exists($key, $this->options)) {
            return true;
        }

        return false;
    }

    /**
     * Returns all first level key names.
     *
     * @return array of keys
     */
    public function getKeys()
    {
        return array_keys($this->options);
    }

    /**
     * @param string $key name of key
     * @param mixed $value value to set for key
     */
    public function offsetSet($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * @param string $key name of key
     *
     * @return mixed value of that key
     *
     * @throws \InvalidArgumentException if key does not exist
     */
    public function offsetGet($key)
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException(sprintf('Key "%s" is not defined.', $key));
        }

        return $this->get($key);
    }

    /**
     * Returns whether the key exists or not.
     *
     * @param string $key name of key to check
     *
     * @return bool true, if key exists; false otherwise
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->options);
    }

    /**
     * Unsets the given key's value if it's set.
     *
     * @param string $key name of key to unset
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        if (isset($this->options[$key])) {
            unset($this->options[$key]);
        }
    }

    /**
     * Returns all first level key names.
     *
     * @return array of keys
     */
    public function keys()
    {
        return array_keys($this->options);
    }

    /**
     * Adds the given options to the current options.
     *
     * @param array $options array of key-value pairs to add to current options
     *
     * @return Options self instance for fluent API
     */
    public function add(array $options = array())
    {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Returns the data as an associative array.
     *
     * @return array with all data
     */
    public function toArray()
    {
        return $this->options;
    }

    /**
     * Delete all key/value pairs.
     */
    public function clear()
    {
        $this->options = array();
    }
}
