<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\System\Tools\JsonTrait;
use ScoutUnitsList\System\Tools\TypesDependencyTrait;

/**
 * AJAX controller
 */
class AjaxController extends Controller
{
    use JsonTrait;
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
        if ($config->areOrderCategoriesDefined()) {
            $term = $this->request->query->getString('term');

            $orders = $this->loader->get('repository.attachment')
                ->findMatchedTitles($term, $config->getOrderCategoryIds());

            foreach ($orders as $id => $title) {
                $list[] =[
                    'id' => $id,
                    'value' => $title,
                ];
            }
        }

        $this->sendResponse($list);
    }
}
