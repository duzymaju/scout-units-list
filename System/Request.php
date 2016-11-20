<?php

namespace ScoutUnitsList\System;

use Exception;

/**
 * System request
 */
class Request
{
    /** @const string */
    const METHOD_GET = 'get';

    /** @const string */
    const METHOD_POST = 'post';

    /** @const string */
    const METHOD_PUT = 'put';

    /** @const string */
    const PROTOCOL_HTTP = 'http';

    /** @const string */
    const PROTOCOL_HTTPS = 'https';

    /** @const string */
    const SESSION_INDEX = 'SID';

    /** @var ParamPack */
    public $query;

    /** @var ParamPack */
    public $request;

    /** @var ParamPack */
    public $params;

    /** @var ParamPack */
    public $files;

    /** @var ParamPack */
    public $cookies;

    /** @var string */
    protected $method;

    /** @var string */
    protected $protocol;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $path;

    /** @var string */
    protected $ip = '';

    /**
     * Constructor
     */
    public function __construct ()
    {
        $this->query = new ParamPack($_GET);
        $this->request = new ParamPack($_POST);
        $this->files = new ParamPack($_FILES);
        $this->cookies = new ParamPack($_COOKIE);

        $this->params = new ParamPack();
        $this->params->addParentPack($this->request)
            ->addParentPack($this->query);
        if ($this->cookies->has(self::SESSION_INDEX)) {
            $_COOKIE[self::SESSION_INDEX] = $this->cookies->get(self::SESSION_INDEX);
        }

        $this->method = strtolower($_SERVER['REQUEST_METHOD']) == self::METHOD_POST ? self::METHOD_POST :
            self::METHOD_GET;
        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? self::PROTOCOL_HTTPS :
            self::PROTOCOL_HTTP;
        $this->domain = $_SERVER['SERVER_NAME'];
        $this->path = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
    }

    /**
     * Set cookie
     *
     * @param string $name     name
     * @param string $value    value
     * @param int    $expire   expire
     * @param string $path     path
     * @param string $domain   domain
     * @param bool   $secure   secure
     * @param bool   $httpOnly HTTP only
     *
     * @return self
     *
     * @throws Exception
     */
    public function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        if (empty($domain)) {
            $domain = $this->getDomain();
        }
        if (!setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly)) {
            throw new Exception('An exception occured during cookie setting.');
        }
        if ($expire == 0 || $expire > time()) {
            $this->cookies->add($name, $value);
        }

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Is GET
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->method == self::METHOD_GET;
    }

    /**
     * Is POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->method == self::METHOD_POST;
    }

    /**
     * Is PUT
     *
     * @return bool
     */
    public function isPut()
    {
        return $this->method == self::METHOD_PUT;
    }

    /**
     * Is valid protocol
     *
     * @param string $protocol protocol
     *
     * @return bool
     */
    public function isValidProtocol($protocol)
    {
        return in_array($protocol, array(
            self::PROTOCOL_HTTP,
            self::PROTOCOL_HTTPS,
        ));
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Get page address
     *
     * @param string|null $protocol protocol
     *
     * @return string
     */
    public function getPageAddress($protocol = null)
    {
        return ($this->isValidProtocol($protocol) ? $protocol : $this->protocol) . '://' . $this->getDomain();
    }

    /**
     * Get URL
     *
     * @param string|null $path         path
     * @param array       $params       params
     * @param bool|string $absolutePath absolute path
     *
     * @return string
     */
    public function getUrl($path = '/', array $params = array(), $absolutePath = false)
    {
        if (!isset($path)) {
            $path = '';
        } elseif (!is_string($path) || empty($path)) {
            $path = '/';
        }
        if ($absolutePath) {
            $path = ($absolutePath === true ? $this->getPageAddress() : ($this->isValidProtocol($absolutePath) ?
                $this->getPageAddress($absolutePath) : $absolutePath)) . $path;
        }

        foreach ($params as $key => $param) {
            if (isset($param) && !is_object($param)) {
                $params[$key] = $this->urlEncodeParam($key, $param);
            } else {
                unset($params[$key]);
            }
        }
        if (count($params) > 0) {
            $path .= '?' . implode('&amp;', $params);
        }

        return $path;
    }

    /**
     * URL encode param
     *
     * @param string $key               key
     * @param mixed  $param             param
     * @param string $keyWithValueJoint key with value joint
     * @param int    $level             level
     *
     * @return string
     */
    protected function urlEncodeParam($key, $param, $keyWithValueJoint = '=', $level = 1)
    {
        if ($level == 1) {
            $key = urlencode($key);
        }
        if (is_array($param)) {
            foreach ($param as $subKey => $subParam) {
                $param[$subKey] = $this->urlEncodeParam($key . '[' . urlencode($subKey) . ']', $subParam,
                    $keyWithValueJoint, $level + 1);
            }
            $encodedParam = implode('&amp;', $param);
        } else {
            $encodedParam = $key . $keyWithValueJoint . urlencode($param);
        }

        return $encodedParam;
    }

    /**
     * Get current URL
     *
     * @param array       $paramsToAdd   params to add
     * @param array|null  $namesToRemove names to remove
     * @param bool|string $absolutePath  absolute path
     *
     * @return string
     */
    public function getCurrentUrl(array $paramsToAdd = array(), array $namesToRemove = null, $absolutePath = false)
    {
        $params = $namesToRemove === true ? $paramsToAdd : array_merge($this->query->getPack(), $paramsToAdd);
        if (is_array($namesToRemove)) {
            foreach ($namesToRemove as $nameToRemove) {
                if (array_key_exists($nameToRemove, $params)) {
                    unset($params[$nameToRemove]);
                }
            }
        }

        return $this->getUrl($this->path, $params, $absolutePath);
    }

    /**
     * Get current URL with only
     *
     * @param array       $namesToKeep  names to keep
     * @param array       $paramsToAdd  params to add
     * @param bool|string $absolutePath absolute path
     * 
     * @return string
     */
    public function getCurrentUrlWithOnly(array $namesToKeep, array $paramsToAdd = array(), $absolutePath = false)
    {
        $params = array();
        foreach ($namesToKeep as $nameToKeep) {
            $param = $this->query->get($nameToKeep);
            if (isset($param)) {
                $params[$nameToKeep] = $param;
            }
        }

        return $this->getUrl($this->path, array_merge($params, $paramsToAdd), $absolutePath);
    }

    /**
     * Get URL with current params
     *
     * @param string      $path            path
     * @param array       $params          params
     * @param array       $paramNames      param names
     * @param bool        $overwriteParams overwriteparams
     * @param bool|string $absolutePath    absolute path
     *
     * @return string
     */
    public function getUrlWithCurrentParams($path = '/', array $params = array(), array $paramNames = array(),
        $overwriteParams = true, $absolutePath = false)
    {
        foreach ($paramNames as $paramName) {
            $param = $this->query->get($paramName);
            if (isset($param) && ($overwriteParams || !isset($params[$paramName]))) {
                $params[$paramName] = $param;
            }
        }

        return $this->getUrl($path, $params, $absolutePath);
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get IP
     *
     * @return string|null
     */
    public function getIp()
    {
        if ($this->ip == '') {
            if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
                $this->ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
                $this->ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $this->ip = null;
            }
        }

        return $this->ip;
    }

    /**
     * Get max filesize
     *
     * @param int|null $maxFileSize max file size
     *
     * @return int
     */
    public function getMaxFileSize($maxFileSize = null)
    {
        $iniMaxFileSize = min($this->getIniInBytes('upload_max_filesize'), $this->getIniInBytes('post_max_size'));
        
        return isset($maxFileSize) ? (int) min($maxFileSize, $iniMaxFileSize) : $iniMaxFileSize;
    }

    /**
     * Get ini in bytes
     *
     * @param string $name name
     *
     * @return int
     */
    protected function getIniInBytes($name)
    {
        $value = trim(ini_get($name));
        switch (strtolower($value[strlen($value) - 1])) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Is post max size exceeded
     *
     * @return bool
     */
    public function isPostMaxSizeExceeded()
    {
        return $this->getMethod() == self::METHOD_POST && empty($this->request) && empty($this->files) &&
            $_SERVER['CONTENT_LENGTH'] > 0 || false;
    }

    /**
     * Is ajax
     *
     * @return bool
     */
    public function isAjax()
    {
        $requestedWith = array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;

        return !empty($requestedWith) && strtolower($requestedWith) == 'xmlhttprequest';
    }
}
