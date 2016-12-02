<?php

namespace ScoutUnitsList\Manager;

/**
 * Cache manager
 */
class CacheManager
{
    /** @var string */
    private $id;

    /** @var string */
    private $path;

    /** @var int */
    private $ttl;

    /** @var array */
    private $data = [];

    /**
     * Constructor
     *
     * @param string $path path
     * @param int    $ttl  TTL
     */
    public function __construct($path, $ttl = 3600)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
        $this->path = $path;
        $this->ttl = $ttl;
    }

    /**
     * Set ID
     *
     * @param string $idString ID string
     *
     * @return bool
     */
    public function setId($idString)
    {
        $this->id = md5($idString);
    }

    /**
     * Get cache file path
     *
     * @return string
     */
    private function getCacheFilePath()
    {
        return $this->path . '/' . $this->id . '.cache';
    }

    /**
     * Has
     *
     * @param int|null $ttl   TTL
     * @param bool     $clear clear
     *
     * @return bool
     */
    public function has($ttl = null, $clear = false)
    {
        if (file_exists($this->getCacheFilePath())) {
            $ttl = isset($ttl) ?: $this->ttl;
            $fileModificationTime = filemtime($this->getCacheFilePath());
            if (!$fileModificationTime || time() - $ttl <= $fileModificationTime) {
                return true;
            } elseif ($clear) {
                unlink($this->getCacheFilePath());
            }
        }

        return false;
    }

    /**
     * Get
     *
     * @param int|null $ttl   TTL
     * @param bool     $clear clear
     *
     * @return string|null
     */
    public function get($ttl = null, $clear = false)
    {
        if (array_key_exists($this->id, $this->data)) {
            $data = $this->data[$this->id];
        } elseif ($this->has($ttl, $clear)) {
            $data = file_get_contents($this->getCacheFilePath());
        } else {
            $data = null;
        }

        return $data;
    }

    /**
     * Set
     *
     * @param string $data data
     *
     * @return self
     */
    public function set($data)
    {
        file_put_contents($this->getCacheFilePath(), $data);
        $this->data[$this->id] = $data;

        return $this;
    }
}
