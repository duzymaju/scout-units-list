<?php

namespace ScoutUnitsList\System\View;

use ScoutUnitsList\System\View;

/**
 * System view partial
 */
class Partial extends Basics
{
    /** @var View */
    protected $view;

    /**
     * Constructor
     *
     * @param View   $view   view
     * @param string $path   path
     * @param string $name   name
     * @param array  $params parameters
     */
    public function __construct(View $view, $path, $name, array $params = [])
    {
        parent::__construct($path, $name, $params);
        $this->view = $view;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->view->getRequest();
    }

    /**
     * Get partial
     *
     * @param string $name   name
     * @param array  $params params
     *
     * @return string
     */
    public function getPartial($name, array $params = [])
    {
        $partial = new Partial($this->view, $this->getPath(), $name, $params);

        return $partial->getRender();
    }

    /**
     * Link
     *
     * @param array       $params     params
     * @param string|null $scriptName script name
     */
    public function link(array $params = [], $scriptName = null)
    {
        $this->view->link($params, $scriptName);
    }

    /**
     * Get link
     *
     * @param array       $params     params
     * @param string|null $scriptName script name
     *
     * @return string
     */
    public function getLink(array $params = [], $scriptName = null)
    {
        return $this->view->getLink($params, $scriptName);
    }
}
