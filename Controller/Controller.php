<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Exception\FormException;
use ScoutUnitsList\Form\Form;
use ScoutUnitsList\Model\ModelInterface;
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
     * Get view path
     *
     * @return string
     */
    private function getViewPath()
    {
        return $this->loader->getPath('View');
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
        if (array_key_exists('path', $params)) {
            $path = $params['path'];
            unset($params['path']);
        } else {
            $path = $this->getViewPath();
        }
        if (!array_key_exists('originalPath', $params)) {
            $params['originalPath'] = $this->getViewPath();
        }

        $view = new View($path, $name, $params);
        $view->setRequest($this->request);

        return $view;
    }

    /**
     * Create form
     * 
     * @param string         $formClassName form class name
     * @param ModelInterface $model         model
     * @param array          $settings      settings
     *
     * @return Form
     *
     * @throws FormException
     */
    public function createForm($formClassName, ModelInterface $model, array $settings = [])
    {
        $form = $this->get($formClassName);
        if (!$form) {
            $form = new $formClassName();
        }
        if (!($form instanceof Form)) {
            throw new FormException(sprintf('Form class "%s" doesn\'t exist.', $formClassName));
        }
        $form->setUp($this->loader, $this->request, $model, $this->getViewPath(), $settings);

        return $form;
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
