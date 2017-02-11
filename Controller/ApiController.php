<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\System\Tools\TypesDependencyTrait;

/**
 * API controller
 */
class ApiController extends Controller
{
    use TypesDependencyTrait;

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
                'value' => $user->getDisplayName() . ' (' . $user->getLogin() . ')',
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
        $childType = $this->request->query->getString('type');
        $types = empty($childType) ? [] : $this->getPossibleParentTypes($childType);

        $units = $this->loader->get('repository.unit')
            ->findByNameAndTypes($term, $types);

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
     * Orders action
     */
    public function ordersAction()
    {
        $list = [];
        $config = $this->get('manager.config')
            ->get();
        if ($config->isOrderCategoryDefined()) {
            $term = $this->request->query->getString('term');

            $orders = $this->loader->get('repository.attachment')
                ->findMatchedTitles($term, $config->getOrderCategoryId());

            foreach ($orders as $id => $title) {
                $list[] =[
                    'id' => $id,
                    'value' => $title,
                ];
            }
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
