<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;

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
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        switch ($action) {
            case 'form':
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $this->formAction($id);
                break;

            case 'delete':
                if (isset($_GET['id'])) {
                    $this->deleteAction($_GET['id']);
                }

            case 'list':
            default:
                $this->listAction();
                break;
        }
    }

    /**
     * Form action
     *
     * @param int|null $id ID
     */
    public function formAction($id)
    {
        $positionRepository = $this->get('repository.position');
        $position = $id > 0 ? $positionRepository->getOneByOr404(array(
                'id' => $id,
            )) : null;

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($position)) {
                $position = new Position();
            }
            $position->setNameMale($_POST['nameMale'])
                ->setNameFemale($_POST['nameFemale'])
                ->setLeader((bool) $_POST['leader']);
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
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'list';
        $order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';
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
