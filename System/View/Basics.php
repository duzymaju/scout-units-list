<?php

namespace ScoutUnitsList\System\View;

use Exception;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\Tools\HelpersTrait;

/**
 * System view basics
 */
abstract class Basics
{
    use HelpersTrait;

    /** @const string */
    const TEMPLATE_EXT = '.phtml';

    /** @var string */
    protected $basicsPath;

    /** @var string */
    protected $basicsName;

    /** @var array */
    protected $basicsParams;

    /**
     * Constructor
     *
     * @param string $path   path
     * @param string $name   file name
     * @param array  $params parameters
     */
    public function __construct($path, $name, array $params = [])
    {
        $this->basicsPath = rtrim($path, '/') . '/';
        $this->basicsName = str_replace('\\', '/', trim($name, '/'));
        $this->basicsParams = $params;
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
        $this->basicsParams[$name] = $value;

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
        $param = isset($this->basicsParams[$name]) ? ($toString ? (is_array($this->basicsParams[$name]) ?
            implode('', $this->basicsParams[$name]) : (string) $this->basicsParams[$name]) :
            $this->basicsParams[$name]) : $defaultValue;

        return $param;
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
        return isset($this->basicsParams[$name]);
    }

    /**
     * Get request
     *
     * @return Request
     */
    abstract public function getRequest();

    /**
     * Partial
     *
     * @param string $name   name
     * @param array  $params params
     */
    public function partial($name, array $params = [])
    {
        echo $this->getPartial($name, $params);
    }

    /**
     * Get partial
     *
     * @param string $name   name
     * @param array  $params params
     *
     * @return string
     */
    abstract public function getPartial($name, array $params = []);

    /**
     * Get path
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->basicsPath;
    }

    /**
     * Render
     */
    public function render()
    {
        echo $this->getRender();
    }

    /**
     * Get render
     *
     * @return string
     *
     * @throws Exception
     */
    public function getRender()
    {
        try {
            $fileName = $this->basicsPath . $this->basicsName . self::TEMPLATE_EXT;
            if (!empty($fileName) && file_exists($fileName)) {
                ob_start();
                include($fileName);
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

        return $view;
    }

    /**
     * Link
     *
     * @param array       $params     params
     * @param string|null $scriptName script name
     */
    abstract public function link(array $params = [], $scriptName = null);

    /**
     * Get link
     *
     * @param array       $params     params
     * @param string|null $scriptName script name
     *
     * @return string
     */
    abstract public function getLink(array $params = [], $scriptName = null);
}
