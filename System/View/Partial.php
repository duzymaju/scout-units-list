<?php

namespace ScoutUnitsList\System\View;

use ScoutUnitsList\System\View;

/**
 * System view partial
 */
class Partial extends Basics
{
    /** @var View|null */
    protected $view;

    /**
     * Set view
     *
     * @param View $view view
     *
     * @return self
     */
    public function setView(View $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get request
     *
     * @return Request|null
     */
    public function getRequest()
    {
        return isset($this->view) ? $this->view->getRequest() : null;
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
        $partial = new Partial($this->getPath(), $name, $params);
        if (isset($this->view)) {
            $partial->setView($this->view);
        }

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
        if (isset($this->view)) {
            $this->view->link($params, $scriptName);
        }
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
        return isset($this->view) ? $this->view->getLink($params, $scriptName) : '';
    }
}
