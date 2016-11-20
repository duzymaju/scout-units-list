<?php

namespace ScoutUnitsList\Manager;

use Exception;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\Tools\HelpersTrait;

/**
 * View manager
 */
class ViewManager
{
    use HelpersTrait;

    /** @var Request */
    protected $request;

    /** @var string */
    protected $fileName;

    /** @var array */
    protected $params;

    /** @var string */
    protected $scriptName;

    /** @var array */
    protected $linkParams = [];

    /**
     * Constructor
     *
     * @param Request $request  request
     * @param string  $fileName file name
     * @param array   $params   parameters
     */
    public function __construct(Request $request, $fileName, array $params = [])
    {
        $this->request = $request;
        $this->fileName = $fileName;
        $this->params = $params;
    }

    /**
     * Set parameter
     *
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return self
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Get parameter
     * 
     * @param string $name         name
     * @param mixed  $defaultValue default value
     * @param bool   $toString     to string
     *
     * @return mixed
     */
    public function getParam($name, $defaultValue = null, $toString = false)
    {
        if ($name == 'request') {
            $param = $this->request;
        } else {
            $param = isset($this->params[$name]) ? ($toString ? (is_array($this->params[$name]) ?
                implode('', $this->params[$name]) : (string) $this->params[$name]) : $this->params[$name]) :
                $defaultValue;
        }

        return $param;
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
     * Get parameter
     *
     * @param string $name name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $param = $this->getParam($name);

        return $param;
    }

    /**
     * Magic isset
     *
     * @param string $name name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * Render
     *
     * @throws Exception
     */
    public function render()
    {
        try {
            if (!empty($this->fileName) && file_exists($this->fileName)) {
                ob_start();
                include($this->fileName);
                $view = ob_get_contents();
                ob_end_clean();
            } else {
                $view = '';
            }
        } catch (Exception $e) {
            if (ob_get_length()) {
                ob_end_clean();
            }
            throw $e;
        }

        echo $view;
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
