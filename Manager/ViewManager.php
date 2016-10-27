<?php

namespace ScoutUnitsList\Manager;

use Exception;

/**
 * View manager
 */
class ViewManager
{
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
     * @param string $fileName file name
     * @param array  $params   parameters
     */
    public function __construct($fileName, array $params = [])
    {
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
        $param = isset($this->params[$name]) ? ($toString ? (is_array($this->params[$name]) ?
            implode('', $this->params[$name]) : (string) $this->params[$name]) : $this->params[$name]) : $defaultValue;

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
     * Escape
     *
     * @param string $text  text
     * @param string $flags flags
     * 
     * @return string
     */
    public function escape($text, $flags = ENT_QUOTES)
    {
        return htmlspecialchars($text, $flags);
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
        $link = $scriptName . $this->getQueryString($params);

        return $link;
    }

    /**
     * Print link
     *
     * @param array       $params     params
     * @param string|null $scriptName script name
     */
    public function printLink(array $params = [], $scriptName = null)
    {
        $link = $this->getLink($params, $scriptName);

        echo $link;
    }

    /**
     * Get query string
     *
     * @param array $params params
     *
     * @return string
     */
    protected function getQueryString(array $params)
    {
        foreach ($params as $key => $value) {
            if (isset($value) && !is_object($value)) {
                $params[$key] = $this->getQueryStringParam($key, $value);
            } else {
                unset($params[$key]);
            }
        }
        $queryString = count($params) > 0 ? '?' . implode('&amp;', $params) : '';

        return $queryString;
    }

    /**
     * Get query string param
     *
     * @param string $key   key
     * @param mixed  $value value
     * @param int    $level level
     *
     * @return string
     */
    private function getQueryStringParam($key, $value, $level = 1)
    {
        if ($level == 1) {
            $key = urlencode($key);
        }
        if (is_array($value)) {
            $subValues = [];
            foreach ($value as $subKey => $subValue) {
                $subValues[] = $this->getQueryStringParam($key . '[' . urlencode($subKey) . ']', $subValue, $level + 1);
            }
            $queryStringParam = implode('&amp;', $subValues);
        } else {
            $queryStringParam = $key . '=' . urlencode($value);
        }

        return $queryStringParam;
    }
}
