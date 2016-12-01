<?php

namespace ScoutUnitsList\System\Tools;

use ArrayObject as Ancestor;

/**
 * System tools array object
 */
class ArrayObject extends Ancestor
{
    /**
     * Unshift
     *
     * @return self
     */
    public function unshift()
    {
        $values = func_get_args();
        foreach ($this as $key => $value) {
            if (is_integer($key)) {
                $values[] = $value;
            } else {
                $values[$key] = $value;
            }
        }
        $this->exchangeArray($values);

        return $this;
    }

    /**
     * Unshift list
     *
     * @param self $listObject list object
     *
     * @return self
     */
    public function unshiftList($listObject)
    {
        if ($listObject instanceof self) {
            call_user_func_array([
                $this,
                'unshift'
            ], (array) $listObject);
        }

        return $this;
    }

    /**
     * Push
     *
     * @param mixed ...$value values
     *
     * @return self
     */
    public function push()
    {
        $values = func_get_args();
        foreach ($values as $value) {
            $this[] = $value;
        }

        return $this;
    }

    /**
     * Push list
     *
     * @param self $listObject list object
     *
     * @return self
     */
    public function pushList($listObject)
    {
     if ($listObject instanceof self) {
        call_user_func_array([
            $this,
            'push'
        ], (array) $listObject);
     }

     return $this;
    }

    /**
     * Shift
     *
     * @return mixed
     */
    public function shift()
    {
        $firstElement = null;
        $values = [];
        foreach ($this as $key => $value) {
            if (isset($firstElement)) {
                if (is_integer($key)) {
                    $values[] = $value;
                } else {
                    $values[$key] = $value;
                }
            } else {
                $firstElement = $value;
            }
        }
        $this->exchangeArray($values);

        return $firstElement;
    }

    /**
     * Pop
     *
     * @return mixed
     */
    public function pop()
    {
        if (count($this) > 0) {
            foreach ($this as $key => $value) {}
            unset($this[$key]);
            return $value;
        }

        return null;
    }

    /**
     * Slice
     *
     * @param int      $offset       offset
     * @param int|null $length       length
     * @param bool     $preserveKeys preserve keys
     *
     * @return array
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return array_slice($this->getArrayCopy(), $offset, $length, $preserveKeys);
    }

    /**
     * Splice
     *
     * @param int      $offset       offset
     * @param int|null $length       length
     * @param mixed    $replacement  replacement
     * @param bool     $preserveKeys preserve keys
     *
     * @return array
     */
    public function splice($offset, $length = null, $replacement = [], $preserveKeys = false)
    {
        if ($preserveKeys) {
            $replacement = $this->slice(0, $offset, true) + (array) $replacement;
            if (isset($length)) {
                if ($offset < 0 && abs($offset) > $this->count()) {
                    $offset = -$this->count();
                }
                if ($length < 0) {
                    $array = $replacement + $this->slice($length, null, true);
                } elseif ($offset < 0 && $offset + $length >= 0) {
                    $array = $replacement;
                } else {
                    $array = $replacement + $this->slice($offset + $length, null, true);
                }
            } else {
                $array = $replacement;
            }
            $slice = $this->slice($offset, $length, true);
        } else {
            $array = $this->getArrayCopy();
            $slice = array_splice($array, $offset, $length, (array) $replacement);
        }
        $this->exchangeArray($array);

        return $slice;
    }
}
