<?php

namespace ScoutUnitsList\Controller;

use Exception;
use ScoutUnitsList\Manager\TypesManager;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\Model\User;
use ScoutUnitsList\System\Tools\JsonTrait;

/**
 * AJAX controller
 */
class AjaxController extends Controller
{
    use JsonTrait;

    /**
     * Users action
     */
    public function usersAction()
    {
        $term = $this->request->query->getString('term');

        $users = $this->loader->get('repository.user')
            ->findByName($term);

        $list = [];
        /** @var User $user */
        foreach ($users as $user) {
            $list[] = [
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
        if (empty($childType)) {
            $types = [];
        } else {
            /** @var TypesManager $typesManager */
            $typesManager = $this->get('manager.types');
            $types = $typesManager->getPossibleParentTypes($childType);
        }

        $units = $this->loader->get('repository.unit')
            ->findByNameAndTypes($term, $types);

        $list = [];
        /** @var Unit $unit */
        foreach ($units as $unit) {
            $nameFull = $unit->getNameFull();
            $list[] = [
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
        /** @var Config $config */
        $config = $this->get('manager.config')
            ->get();
        if ($config->areOrderCategoriesDefined()) {
            $term = $this->request->query->getString('term');

            $orders = $this->loader->get('repository.attachment')
                ->findMatchedTitles($term, $config->getOrderCategoryIds());

            foreach ($orders as $id => $title) {
                $list[] = [
                    'id' => $id,
                    'value' => $title,
                ];
            }
        }

        $this->sendResponse($list);
    }

    /**
     * Persons sort action
     */
    public function personsSortAction()
    {
        if (!current_user_can('sul_manage_persons')) {
            $this->respondWith401();
        }

        $order = $this->request->request->getArray('order', []);
        $unit = $this->loader->get('repository.unit')
            ->getOneBy([
                'id' => $this->request->request->getInt('unitId'),
            ]);
        if (count($order) < 1 || !isset($unit)) {
            $this->respondWith404();
        }
        foreach ($order as $key => $id) {
            $order[$key] = (int) $id;
        }

        try {
            $this->loader->get('repository.person')
                ->sortPersonsForUnit($unit, $this->loader->get('repository.position'), $order);
            $this->sendResponse([
                'error' => 0,
            ]);
        } catch (Exception $e) {
            unset($e);
            $this->sendResponse([
                'error' => 1,
            ]);
        }
    }

    /**
     * Respond with 401
     */
    public function respondWith401()
    {
        $this->sendResponse('', 401 . ' ' . $this->request->getResponseStatusName(401));
    }

    /**
     * Respond with 404
     */
    public function respondWith404()
    {
        $this->sendResponse('', 404 . ' ' . $this->request->getResponseStatusName(404));
    }
}
