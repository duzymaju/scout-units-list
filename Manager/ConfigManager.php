<?php

namespace ScoutUnitsList\Manager;

use ScoutUnitsList\Exception\ConfigException;

/**
 * Configuration manager
 */
class ConfigManager
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $options = [];

    /**
     * Constructor
     *
     * @param string $name name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->reload();
    }

    /**
     * Reload options
     *
     * @return self
     */
    public function reload()
    {
        $this->options = get_option($this->name, $this->options);

        return $this;
    }

    /**
     * Get option
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function get($name, $defaultValue = null)
    {
        $option = array_key_exists($name, $this->options) ? $this->options[$name] : $defaultValue;

        return $option;
    }

    /**
     * Get all
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->options;
    }

    /**
     * Set option
     *
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return self
     */
    public function set($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Delete option
     *
     * @param string $name name
     *
     * @return self
     */
    public function delete($name)
    {
        if (array_key_exists($name, $this->options)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    /**
     * Save options
     *
     * @return self
     *
     * @throws ConfigException
     */
    public function save()
    {
        update_option($this->name, $this->options);

        return $this;
    }

    /**
     * Remove options
     *
     * @throws ConfigException
     */
    public function remove()
    {
        if (delete_option($this->name) === false) {
            throw new ConfigException('An error occured during configuration removing.');
        }
    }
}
