<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Form\UnitAdminForm;
use ScoutUnitsList\Form\UnitLeaderForm;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\System\Request;

/**
 * Admin units controller
 */
class UnitsController extends BasicController
{
    /** @const string */
    const PAGE_NAME = 'sul-units';

    /**
     * Routes
     */
    public function routes()
    {
        $request = $this->request;
        $action = $request->query->getString('action', 'list');

        try {
            $id = $request->query->getInt('id');

            switch ($action) {
                case 'admin_form':
                    $this->adminFormAction($request, $id);
                    break;

                case 'leader_form':
                    $this->leaderFormAction($request, $id);
                    break;

                case 'delete':
                    if (isset($id)) {
                        $this->deleteAction($id);
                    }

                case 'list':
                default:
                    $this->listAction();
                    break;
            }
        } catch (NotFoundException $e) {
            $this->respondWith404($e);
        }
    }

    /**
     * Admin form action
     *
     * @param Request  $request request
     * @param int|null $id      ID
     */
    public function adminFormAction(Request $request, $id = null)
    {
        $unitRepository = $this->get('repository.unit');
        $unit = $id > 0 ? $unitRepository->getOneByOr404(array(
                'id' => $id,
            )) : new Unit();

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        $form = new UnitAdminForm($request, $unit);
        if ($form->isValid()) {
            try {
                // @TODO: set proper slug here instead of inside model - check if there is no duplication
                $unitRepository->save($unit);
                $messageManager->addSuccess(__('Unit was successfully saved.', $td));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during unit saving.', $td));
            }
        }

        $this->getView('Admin/Units/AdminForm', array(
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'td' => $this->loader->getName(),
            'unit' => $unit,
        ))->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Leader form action
     *
     * @param Request $request request
     * @param int     $id      ID
     */
    public function leaderFormAction(Request $request, $id)
    {
        $unitRepository = $this->get('repository.unit');
        $unit = $unitRepository->getOneByOr404(array(
            'id' => $id,
        ));

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        $form = new UnitLeaderForm($request, $unit);
        if ($form->isValid()) {
            try {
                $unitRepository->save($unit);
                $messageManager->addSuccess(__('Unit was successfully saved.', $td));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during unit saving.', $td));
            }
        }

        $this->getView('Admin/Units/LeaderForm', array(
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'td' => $this->loader->getName(),
            'unit' => $unit,
        ))->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Delete action
     *
     * @param int $id ID
     */
    public function deleteAction($id)
    {
        $unitRepository = $this->get('repository.unit');
        $unit = $unitRepository->getOneByOr404(array(
            'id' => $id,
        ));

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        try {
            $unitRepository->delete($unit);
            $messageManager->addSuccess(__('Unit was successfully deleted.', $td));
        } catch (Exception $e) {
            unset($e);
            $messageManager->addError(__('An error occured during unit removing.', $td));
        }
    }

    /**
     * List action
     */
    public function listAction()
    {
        $units = $this->get('repository.unit')
            ->getBy(array());

        $this->getView('Admin/Units/List', array(
            'messages' => $this->get('manager.message')
                ->getMessages(),
            'td' => $this->loader->getName(),
            'units' => $units,
        ))->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
