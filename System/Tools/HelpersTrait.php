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
}
