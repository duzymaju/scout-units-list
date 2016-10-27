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
                break;

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
        $position = $this->get('repository.position')
            ->getOneBy([
                'id' => $id,
            ]);
        if (!isset($position)) {
            return; // @TODO: 404
        }

        $this->getView('Admin/Positions/Form', [
            'position' => $position,
            'td' => $this->loader->getName(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Delete action
     *
     * @param int $id ID
     */
    public function deleteAction($id)
    {
        $this->getView('Admin/Positions/Delete', [
            'td' => $this->loader->getName(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * List action
     */
    public function listAction()
    {
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'list';
        $order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';

        $this->getView('Admin/Positions/List', [
            'td' => $this->loader->getName(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
