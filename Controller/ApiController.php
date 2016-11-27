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

        $users = $this->loader->get('repository.user')
            ->findByName($term);

        $list = [];
        foreach ($users as $user) {
            $list[] =[
                'id' => $user->getId(),
                'value' => $user->getNiceName() . ' (' . $user->getLogin() . ')',
            ];
        }

        $this->sendResponse($list);
    }

    /**
     * Units action
     *
     * @TODO: prevent returning back current unit; return only these with proper type/subtype
     */
    public function unitsAction()
    {
        $term = $this->request->query->getString('term');

        $units = $this->loader->get('repository.unit')
            ->findByName($term);

        $list = [];
        foreach ($units as $unit) {
            $nameFull = $unit->getNameFull();
            $list[] =[
                'id' => $unit->getId(),
                'value' => empty($nameFull) ? $unit->getName() : $nameFull,
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
