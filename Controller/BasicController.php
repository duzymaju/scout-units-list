<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Loader;
use ScoutUnitsList\Manager\ViewManager;

/**
 * Basic controller
 */
abstract class BasicController
{
    /** @var Loader */
    protected $loader;

    /**
     * Constructor
     *
     * @param Loader $loader loader
     */
    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get service
     *
     * @param string $name name
     *
     * @return object|null
     */
    public function get($name)
    {
        return $this->loader->get($name);
    }

    /**
     * Get view
     *
     * @param string $name   name
     * @param array  $params parameters
     *
     * @return ViewManager
     */
    public function getView($name, array $params = [])
    {
        $viewFileName = $this->loader->getPath('View/' . $name . '.phtml');
        $viewManager = new ViewManager($viewFileName, $params);

        return $viewManager;
    }
}
