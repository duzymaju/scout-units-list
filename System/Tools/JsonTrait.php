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
     * @param array $data data
     */
    protected function sendResponse(array $data)
    {
        header('Content-Type: text/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s T'));
        echo json_encode($data);
        exit;
    }
}
