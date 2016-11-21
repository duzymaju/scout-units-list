<?php

namespace ScoutUnitsList\Controller;

/**
 * API controller
 */
class ApiController extends BasicController
{
    /**
     * Users action
     */
    public function usersAction()
    {
        $term = $this->request->query->getString('term');

        $dbManager = $this->loader->get('manager.db');
        $query = $dbManager->prepare('SELECT ID, user_login, user_nicename FROM wp_users ' .
                'WHERE user_login LIKE "%%:term%%" || user_nicename LIKE "%%:term%%" LIMIT 10')
            ->setParam('term', $term)
            ->getQuery();
        $users = $dbManager->getResults($query, ARRAY_A);

        $list = [];
        foreach ($users as $user) {
            $list[] =[
                'id' => $user['ID'],
                'value' => $user['user_nicename'] . ' (' . $user['user_login'] . ')',
            ];
        }

        $this->sendResponse($list);
    }

    /**
     * Send response
     *
     * @param array $data data
     */
    private function sendResponse(array $data)
    {
        header('Content-Type: text/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s T'));
        echo json_encode($data);
        exit; // @TODO: remove it after resolving problem with additional characters
    }
}
