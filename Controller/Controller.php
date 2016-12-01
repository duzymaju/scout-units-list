<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\System\Loader;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\View;

/**
 * Controller
 */
abstract class Controller
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
     * @return View
     */
    public function getView($name, array $params = [])
    {
        $path = $this->loader->getPath('View');
        $view = new View($this->request, $path, $name, $params);

        return $view;
    }

    /**
     * Respond with 401
     */
    public function respondWith401()
    {
        $this->getView('Admin/Error401')
            ->render();
    }

    /**
     * Respond with 404
     */
    public function respondWith404()
    {
        $this->getView('Admin/Error404')
            ->render();
    }
}
