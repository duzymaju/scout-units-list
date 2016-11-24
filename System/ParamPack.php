<?php

namespace ScoutUnitsList\System;

use stdClass;
use Traversable;

/**
 * System param pack
 */
class ParamPack
{
    /** @var array */
    protected $params;

    /** @var array */
    protected $parentPacks = [];

    /**
     * Constructor
     *
     * @param array $params params
     */
    public function __construct(array $params = [])
    {
        $this->params = $this->inputFilter($params);
    }

    /**
     * Add parent pack
     *
     * @param self $parentPack parent pack
     *
     * @return self
     */
    public function addParentPack(self $parentPack)
    {
        $this->parentPacks[] = $parentPack;

        return $this;
    }

    /**
     * Get
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function get($name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->params)) {
            $param = $this->stripSlashes($this->params[$name]);
        } else {
            foreach ($this->parentPacks as $parentPack) {
                $param = $parentPack->get($name);
                if (isset($param)) {
                    break;
                }
            }
            if (!isset($param)) {
                $param = $defaultValue;
            }
        }

        return $param;
    }

    /**
     * Get string
     *
     * @param string   $name         name
     * @param mixed    $defaultValue default value
     * @param int|null $length       length
     *
     * @return mixed
     */
    public function getString($name, $defaultValue = null, $length = null)
    {
        $param = $this->get($name);
        if (isset($param)) {
            $param = is_int($length) && $length > 0 ? mb_substr((string) $param, 0, $length) : (string) $param;
        } else {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Get integer
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getInt($name, $defaultValue = null)
    {
        $param = $this->get($name);
        if (isset($param)) {
            $param = (int) $param;
        } else {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Get float
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getFloat($name, $defaultValue = null)
    {
        $param = $this->get($name);
        if (isset($param)) {
            $param = (float) (is_string($param) && preg_match('#^-?[0-9]*,[0-9]+$#', $param) ?
                str_replace(',', '.', $param) : $param);
        } else {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Get boolean
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getBool($name, $defaultValue = null)
    {
        $param = $this->get($name);
        if (isset($param)) {
            $param = !in_array($param, ['false', 'null', '', '0']) || false;
        } else {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Get array
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getArray($name, $defaultValue = null)
    {
        $param = $this->get($name);
        if (isset($param)) {
            $param = (array) $param;
        } else {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Get object
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getObject($name, $defaultValue = null)
    {
        $param = $this->get($name);
        if (isset($param)) {
            $param = (object) $param;
        } else {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Add
     *
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return self
     */
    public function add($name, $value)
    {
        $this->params[$name] = $this->inputFilter($value);

        return $this;
    }

    /**
     * Get pack
     *
     * @return array
     */
    public function getPack()
    {
        return $this->params;
    }

    /**
     * Has
     * 
     * @param string $name name
     *
     * @return bool
     */
    public function has($name)
    {
        $param = $this->get($name);

        return isset($param);
    }

    /**
     * Strip slashes
     *
     * @param mixed $value value
     *
     * @return mixed
     */
    protected function stripSlashes($value)
    {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (is_array($value)) {
                $value = $this->stripSlashesRecursively($value);
            } elseif (is_string($value)) {
                $value = stripslashes($value);
            }
        }

        return $value;
    }

    /**
     * Strip slashes recursively
     *
     * @param array $array array
     *
     * @return array
     */
    protected function stripSlashesRecursively($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->stripSlashesRecursively($value);
            } elseif (is_string($value)) {
                $array[$key] = stripslashes($value);
            }
        }

        return $array;
    }

    /**
     * Input filter
     *
     * @param mixed $input input
     *
     * @return mixed
     */
    protected function inputFilter($input)
    {
        if (is_string($input)) {
            $input = trim(wp_check_invalid_utf8($input, true));
        } elseif (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->filter($value);
            }
        } elseif (is_object($input) && ($input instanceof stdClass || $input instanceof Traversable)) {
            foreach ($input as $key => $value) {
                $input->$key = $this->filter($value);
            }
        }

        return $input;
    }
}
