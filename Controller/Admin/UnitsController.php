<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\Controller;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Exception\UnauthorizedException;
use ScoutUnitsList\Form\PersonForm;
use ScoutUnitsList\Form\UnitAdminForm;
use ScoutUnitsList\Form\UnitLeaderForm;
use ScoutUnitsList\Model\Person;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\Tools\Paginator;
use Traversable;

/**
 * Admin units controller
 */
class UnitsController extends Controller
{
    /** @const string */
    const PAGE_NAME = 'sul-units';

    /**
     * Routes
     */
    public function routes()
    {
        try {
            $request = $this->request;
            $action = $request->query->getString('action', 'list');
            $id = $request->query->getInt('id');

            switch ($action) {
                case 'admin-form':
                    $this->adminFormAction($request, $id);
                    break;

                case 'leader-form':
                    $this->leaderFormAction($id);
                    break;

                case 'person-manage':
                    $this->personManageAction($request, $id);
                    break;

                case 'delete':
                    if (isset($id)) {
                        $this->deleteAction($id);
                    }

                case 'list':
                default:
                    $this->listAction($request);
                    break;
            }
        } catch (UnauthorizedException $e) {
            $this->respondWith401($e);
        } catch (NotFoundException $e) {
            $this->respondWith404($e);
        }
    }

    /**
     * Admin form action
     *
     * @param Request  $request request
     * @param int|null $id      ID
     *
     * @throws UnauthorizedException
     */
    public function adminFormAction(Request $request, $id = null)
    {
        if (!current_user_can('sul_manage_units')) {
            throw new UnauthorizedException();
        }

        $attachmentRepository = $this->get('repository.attachment');
        $unitRepository = $this->get('repository.unit');
        $unit = $id > 0 ? $unitRepository->getOneByOr404([
                'id' => $id,
            ]) : new Unit();

        $messageManager = $this->get('manager.message');

        $parentId = $request->request->getInt('parentId', $unit->getParentId());
        $orderId = $request->request->getInt('orderId', $unit->getOrderId());
        $config = $this->get('manager.config')
            ->get();
        $orderCategoryDefined = $config->getOrderCategoryId() > 0;
        $form = $this->createForm(UnitAdminForm::class, $unit, [
            'config' => $config,
            'order' => $orderCategoryDefined && $orderId > 0 ? $attachmentRepository->getOneBy([
                'id' => $orderId,
            ]) : null,
            'parentUnit' => $parentId > 0 ? $unitRepository->getOneBy([
                'id' => $parentId,
            ]) : null,
            'validator' => [
                'repository' => $unitRepository,
            ],
        ]);
        if ($form->isValid()) {
            try {
                if (empty($id)) {
                    $unit->setSlug($unitRepository->getUniqueSlug($unit));
                }
                $unitRepository->save($unit);
                $messageManager->addSuccess(__('Unit was successfully saved.', 'scout-units-list'));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during unit saving.', 'scout-units-list'));
            }
        }

        $this->getView('Admin/Units/AdminForm', [
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'orderCategoryDefined' => $orderCategoryDefined,
            'unit' => $unit,
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Leader form action
     *
     * @param int $id ID
     *
     * @throws UnauthorizedException
     */
    public function leaderFormAction($id)
    {
        $unitRepository = $this->get('repository.unit');
        $unit = $unitRepository->getOneByOr404([
            'id' => $id,
        ]);

        if (!current_user_can('sul_manage_units')) {
            if (!current_user_can('sul_modify_own_units')) {
                throw new UnauthorizedException();
            }
            $personRepository = $this->get('repository.person');
            $userRepository = $this->get('repository.user');
            if (!$personRepository->isUnitLeader($userRepository->getCurrentUser(), $unit)) {
                throw new UnauthorizedException();
            }
        }

        $messageManager = $this->get('manager.message');

        $form = $this->createForm(UnitLeaderForm::class, $unit);
        if ($form->isValid()) {
            try {
                $unitRepository->save($unit);
                $messageManager->addSuccess(__('Unit was successfully saved.', 'scout-units-list'));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during unit saving.', 'scout-units-list'));
            }
        }

        $this->getView('Admin/Units/LeaderForm', [
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'unit' => $unit,
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Person manage action
     *
     * @param Request $request request
     * @param int     $id      ID
     *
     * @throws UnauthorizedException
     */
    public function personManageAction($request, $id)
    {
        if (!current_user_can('sul_manage_persons')) {
            throw new UnauthorizedException();
        }

        $unitRepository = $this->get('repository.unit');
        $unit = $unitRepository->getOneByOr404([
            'id' => $id,
        ]);

        $messageManager = $this->get('manager.message');

        $positionList = [];
        $positionRepository = $this->get('repository.position');
        $positions = $positionRepository->getBy([
            'type' => $unit->getType(),
        ]);
        foreach ($positions as $position) {
            $positionList[$position->getId()] = $position->getNameMale() . '/' . $position->getNameFemale();
        }

        $config = $this->get('manager.config')
            ->get();
        $orderCategoryDefined = $config->getOrderCategoryId() > 0;

        $personRepository = $this->get('repository.person');
        $userRepository = $this->get('repository.user');
        if (count($positionList) > 0) {
            $person = new Person();
            $person->setUnitId($id);
            $userId = $request->request->getInt('userId');
            $orderId = $request->request->getInt('orderId');
            $attachmentRepository = $this->get('repository.attachment');
            $form = $this->createForm(PersonForm::class, $person, [
                'action' => $request->getCurrentUrl([], [
                    'deletedId',
                ]),
                'config' => $config,
                'order' => $orderCategoryDefined && $orderId > 0 ? $attachmentRepository->getOneBy([
                    'id' => $orderId,
                ]) : null,
                'positions' => $positionList,
                'user' => $userId > 0 ? $userRepository->getOneBy([
                    'id' => $userId,
                ]) : null,
                'validator' => [
                    'repository' => $personRepository,
                ],
            ]);
        } else {
            $messageManager->addWarning(__('Add at least one position for this unit type to be able to manage persons.',
                'scout-units-list'));
            $form = null;
        }

        $deletedId = $request->query->getInt('deletedId');
        if (isset($deletedId)) {
            $person = $personRepository->getOneBy([
                'id' => $deletedId,
            ]);
            if (isset($person)) {
                try {
                    $personRepository->delete($person);
                    $messageManager->addSuccess(__('Person was successfully deleted.', 'scout-units-list'));
                } catch (Exception $e) {
                    unlink($e);
                    $messageManager->addError(__('An error occured during person removing.', 'scout-units-list'));
                }
            }
        } elseif (isset($form) && $form->isValid()) {
            try {
                $personRepository->save($person);
                $messageManager->addSuccess(__('Person was successfully saved.', 'scout-units-list'));
                $form->clear();
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during person saving.', 'scout-units-list'));
            }
        }
        $personRepository->setPersonsToUnits([
            $unit,
        ], $userRepository, $positionRepository, true, false);

        $this->getView('Admin/Units/PersonManage', [
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'orderCategoryDefined' => $orderCategoryDefined,
            'unit' => $unit,
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Delete action
     *
     * @param int $id ID
     *
     * @throws UnauthorizedException
     */
    public function deleteAction($id)
    {
        if (!current_user_can('sul_manage_units')) {
            throw new UnauthorizedException();
        }

        $unitRepository = $this->get('repository.unit');
        $unit = $unitRepository->getOneByOr404([
            'id' => $id,
        ]);

        $messageManager = $this->get('manager.message');

        try {
            $unitRepository->delete($unit);
            $messageManager->addSuccess(__('Unit was successfully deleted.', 'scout-units-list'));
        } catch (Exception $e) {
            unset($e);
            $messageManager->addError(__('An error occured during unit removing.', 'scout-units-list'));
        }
    }

    /**
     * List action
     *
     * @param Request $request request
     *
     * @throws UnauthorizedException
     */
    public function listAction(Request $request)
    {
        if (!current_user_can('sul_manage_units') && !current_user_can('sul_modify_own_units')) {
            throw new UnauthorizedException();
        }

        $order = $this->getOrder($request->query);
        $page = max(1, $request->query->getInt('paged', 1));

        $conditions = [];
        if (!current_user_can('sul_manage_units')) {
            $user = $this->get('repository.user')
                ->getCurrentUser();
            $conditions['id'] = $this->get('repository.person')
                ->getSubordinateUnitIds($user);
        }
        $unitRepository = $this->get('repository.unit');
        $units = !array_key_exists('id', $conditions) || count($conditions['id']) > 0 ?
            $unitRepository->getPaginatorBy($conditions, $order, 20, $page) : [];
        $this->setParentUnits($units);

        $this->getView('Admin/Units/List', [
            'messages' => $this->get('manager.message')
                ->getMessages(),
            'units' => $units,
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Set parent units
     *
     * @param Traversable $units units
     *
     * @return self
     */
    private function setParentUnits(Traversable $units)
    {
        $parentIds = [];
        foreach ($units as $unit) {
            if ($unit->getParentId() > 0) {
                $parentIds[] = $unit->getParentId();
            }
        }
        $parentsList = count($parentIds) > 0 ? $this->get('repository.unit')
            ->getBy([
                'id' => array_unique($parentIds),
            ]) : [];

        $parents = [];
        foreach ($parentsList as $parent) {
            $parents[$parent->getId()] = $parent;
        }
        foreach ($units as $unit) {
            if (array_key_exists($unit->getParentId(), $parents)) {
                $unit->setParent($parents[$unit->getParentId()]);
            }
        }

        return $this;
    }

    /**
     * Get order
     *
     * @param ParamPack $params params
     *
     * @return array
     */
    private function getOrder(ParamPack $params)
    {
        $orderBy = $params->getOption('orderby', [
            'address',
            'mail',
            'name',
            'subtype',
            'type',
            'url',
        ], 'default');
        $orderDirection = $params->getString('order') == Paginator::ORDER_DESC ?: Paginator::ORDER_ASC;

        if ($orderBy == 'default') {
            $order = [
                'parentId' => $orderDirection,
                'sort' => $orderDirection,
            ];
        } else {
            $order = [
                $orderBy => $orderDirection,
            ];
        }

        return $order;
    }
}
