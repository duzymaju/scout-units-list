<?php

namespace ScoutUnitsList\System\Tools;

use DateTime as Ancestor;

/**
 * System tools date time
 */
class DateTime extends Ancestor
{
    /** @const string */
    const FORMAT_MYSQL = 'Y-m-d H:i:s';

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format(self::FORMAT_MYSQL);
    }
}
