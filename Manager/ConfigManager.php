<?php

namespace ScoutUnitsList\Manager;

use ScoutUnitsList\Exception\ConfigException;
use ScoutUnitsList\Model\Config as Model;

/**
 * Configuration manager
 */
class ConfigManager
{
    /** @var string */
    protected $name;

    /** @var Model */
    protected $model;

    /**
     * Constructor
     *
     * @param string $name name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->model = new Model();
        $this->reload();
    }

    /**
     * Reload options
     *
     * @return self
     */
    public function reload()
    {
        $this->model->setStructure(get_option($this->name, $this->model->getStructure()));

        return $this;
    }

    /**
     * Get options
     *
     * @return Model
     */
    public function get()
    {
        return $this->model;
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
        update_option($this->name, $this->model->getStructure());

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
