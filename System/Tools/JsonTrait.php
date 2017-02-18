<?php

namespace ScoutUnitsList\System\Tools;

/**
 * System tools types JSON trait
 */
trait JsonTrait
{
    /**
     * Send response
     *
     * @param array|string $data data
     */
    protected function sendResponse($data)
    {
        header('Content-Type: text/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s T'));
        echo is_string($data) ? $data : json_encode($data);
        exit;
    }
}
