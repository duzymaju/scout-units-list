<?php

namespace ScoutUnitsList\System\Tools;

/**
 * System tools helpers trait
 */
trait HelpersTrait
{
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
     * Get attribute
     *
     * @param string $key       key
     * @param mixed  $value     value
     * @param bool   $omitEmpty omit empty
     *
     * @return string
     */
    public function getAttr($key, $value, $omitEmpty = true)
    {
        return empty($value) && $omitEmpty ? '' : ' ' . $this->escape($key) . '="' . $this->escape($value) . '"';
    }
}
