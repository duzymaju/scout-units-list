<?php

namespace ScoutUnitsList\System;

use ScoutUnitsList\System\View\Basics;
use ScoutUnitsList\System\View\Partial;

/**
 * System view
 */
class View extends Basics
{
    /** @var Request */
    protected $request;

    /** @var string */
    protected $scriptName;

    /** @var array */
    protected $linkParams = [];

    /**
     * Constructor
     *
     * @param Request $request request
     * @param string  $path    path
     * @param string  $name    name
     * @param array   $params  parameters
     */
    public function __construct(Request $request, $path, $name, array $params = [])
    {
        parent::__construct($path, $name, $params);
        $this->request = $request;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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
        $partial = new Partial($this, $this->getPath(), $name, $params);

        return $partial->getRender();
    }

    /**
     * Set link data
     *
     * @param string $scriptName script name
     * @param string $pageName   page name
     *
     * @return self
     */
    public function setLinkData($scriptName, $pageName)
    {
        $this->scriptName = $scriptName;
        $this->linkParams = [
            'page' => $pageName,
        ];

        return $this;
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
        if (empty($scriptName)) {
            $scriptName = $this->scriptName;
            $params = array_merge($this->linkParams, $params);
        }
        $link = $this->request->getUrl($scriptName, $params);

        return $link;
    }

    /**
     * Link
     *
     * @param array       $params     params
     * @param string|null $scriptName script name
     */
    public function link(array $params = [], $scriptName = null)
    {
        $link = $this->getLink($params, $scriptName);

        echo $link;
    }
}
