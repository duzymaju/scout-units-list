<?php

namespace ScoutUnitsList\System;

/**
 * System loader
 */
final class Loader
{
    /** @var self */
    private static $instance;

    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /** @var string */
    private $mainPath;

    /** @var string */
    private $dirName;

    /** @var string */
    private $mainUrl;

    /** @var string */
    private $prefix;

    /** @var array */
    private $services = [];

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->mainPath = dirname(plugin_dir_path(__FILE__));
        $this->mainUrl = dirname(plugin_dir_url(__FILE__));

        $this->dirName = basename($this->mainPath);
        $this->name = $this->dirName;

        $namespaceParts = explode('\\', __NAMESPACE__);
        $this->prefix = array_shift($namespaceParts) . '\\';

        spl_autoload_register([
            $this,
            'loadClass'
        ]);
    }

    /**
     * Get instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Run
     *
     * @return self
     */
    public static function run()
    {
        return self::getInstance();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set version
     *
     * @param string $version version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get file URL
     *
     * @param string $filePath file path
     *
     * @return string
     */
    public function getFileUrl($filePath)
    {
        $fileUrl = $this->mainUrl . '/' . ltrim($filePath);

        return $fileUrl;
    }

    /**
     * Load class
     *
     * @param string $className class name
     */
    public function loadClass($className)
    {
        if (strpos($className, $this->prefix) === 0) {
            $relativePath = str_replace('\\', '/', substr($className, strlen($this->prefix))) . '.php';
            require_once($this->getPath($relativePath));
        }
    }

    /**
     * Get path
     *
     * @param string $relativePath relative path
     *
     * @return string
     */
    public function getPath($relativePath = '')
    {
        $absolutePath = $this->mainPath . (empty($relativePath) ? '' : '/' . $relativePath);

        return $absolutePath;
    }

    /**
     * Get dir name
     *
     * @return string
     */
    public function getDirName()
    {
        return $this->dirName;
    }

    /**
     * Set service
     *
     * @param string $name    name
     * @param object $service service
     *
     * @return self
     */
    public function set($name, $service)
    {
        $this->services[$name] = $service;

        return $this;
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
        return array_key_exists($name, $this->services) ? $this->services[$name] : null;
    }

    /**
     * Initialization
     */
    public function init()
    {
        if (is_textdomain_loaded($this->name)) {
            return true;
        } else {
            return load_plugin_textdomain($this->name, false, $this->getDirName() . '/Languages' );
        }
    }
}
