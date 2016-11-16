<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Manager\ViewManager;
use ScoutUnitsList\System\Loader;
use ScoutUnitsList\System\Request;

/**
 * Basic controller
 */
abstract class BasicController
{
    /** @var Loader */
    protected $loader;

    /** @var Request */
    protected $request;

    /**
     * Constructor
     *
     * @param Loader  $loader  loader
     * @param Request $request request
     */
    public function __construct(Loader $loader, Request $request)
    {
        $this->loader = $loader;
        $this->request = $request;
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

    /**
     * Respond with 404
     */
    public function respondWith404()
    {
        $this->getView('Admin/Error404', [
            'td' => $this->loader->getName(),
        ])->render();
    }
}
