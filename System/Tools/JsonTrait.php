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
     * @param array|string $data   data
     * @param int|string   $status status
     */
    protected function sendResponse($data, $status = 200)
    {
        if ($status != 200 || (is_string($status) && strpos($status, '200') !== 0)) {
            header($_SERVER['SERVER_PROTOCOL'] . ': ' . $status);
            header('Status: ' . $status);
        }
        header('Content-Type: text/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s T'));
        echo is_string($data) ? $data : json_encode($data);
        exit;
    }
}
