<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;
use ScoutUnitsList\System\Request;

/**
 * Admin positions controller
 */
class PositionsController extends BasicController
{
    /** @const string */
    const PAGE_NAME = 'sul-positions';

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
                case 'form':
                    $this->formAction($request, $id);
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
     * Form action
     *
     * @param Request  $request request
     * @param int|null $id      ID
     */
    public function formAction(Request $request, $id)
    {
        $positionRepository = $this->get('repository.position');
        $position = $id > 0 ? $positionRepository->getOneByOr404(array(
                'id' => $id,
            )) : null;

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        if ($request->isPost()) {
            if (!isset($position)) {
                $position = new Position();
            }
            $position->setNameMale($request->request->getString('nameMale'))
                ->setNameFemale($request->request->getString('nameFemale'))
                ->setLeader($request->request->getBool('leader'));
            try {
                $positionRepository->save($position);
                $messageManager->addSuccess(__('Position was successfully saved.', $td));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during position saving.', $td));
            }
        }

        $this->getView('Admin/Positions/Form', array(
            'messages' => $messageManager->getMessages(),
            'position' => $position,
            'td' => $this->loader->getName(),
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
        $positionRepository = $this->get('repository.position');
        $position = $positionRepository->getOneByOr404(array(
            'id' => $id,
        ));

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');
        
        try {
            $positionRepository->delete($position);
            $messageManager->addSuccess(__('Position was successfully deleted.', $td));
        } catch (Exception $e) {
            unset($e);
            $messageManager->addError(__('An error occured during position removing.', $td));
        }
    }

    /**
     * List action
     */
    public function listAction()
    {
        $positions = $this->get('repository.position')
            ->getBy(array());

        $this->getView('Admin/Positions/List', array(
            'messages' => $this->get('manager.message')
                ->getMessages(),
            'positions' => $positions,
            'td' => $this->loader->getName(),
        ))->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
