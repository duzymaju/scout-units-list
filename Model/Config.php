<?php

namespace ScoutUnitsList\Model;

/**
 * Configuration model
 */
class Config implements ModelInterface
{
    /** @var string|null */
    protected $orderNoFormat;

    /** @var string|null */
    protected $orderNoPlaceholder;

    /**
     * Get order number format
     *
     * @return string|null
     */
    public function getOrderNoFormat()
    {
        return $this->orderNoFormat;
    }

    /**
     * Set order number format
     *
     * @param string|null $orderNoFormat order number format
     *
     * @return self
     */
    public function setOrderNoFormat($orderNoFormat)
    {
        $this->orderNoFormat = empty($orderNoFormat) ? null : $orderNoFormat;

        return $this;
    }

    /**
     * Get order number placeholder
     *
     * @return string|null
     */
    public function getOrderNoPlaceholder()
    {
        return $this->orderNoPlaceholder;
    }

    /**
     * Set order number placeholder
     *
     * @param string|null $orderNoPlaceholder order number placeholder
     *
     * @return self
     */
    public function setOrderNoPlaceholder($orderNoPlaceholder)
    {
        $this->orderNoPlaceholder = empty($orderNoPlaceholder) ? null : $orderNoPlaceholder;

        return $this;
    }

    /**
     * Get structure
     *
     * @return array
     */
    public function getStructure()
    {
        $structure = [];
        foreach (get_object_vars($this) as $key => $value) {
            $structure[$key] = $value;
        }

        return $structure;
    }

    /**
     * Set structure
     *
     * @param array $structure structure
     *
     * @return self
     */
    public function setStructure(array $structure)
    {
        foreach (array_keys(get_object_vars($this)) as $key) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method(array_key_exists($key, $structure) ? $structure[$key] : null);
            }
        }

        return $this;
    }
}
