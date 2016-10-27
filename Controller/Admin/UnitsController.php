<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;

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
        $this->getView('Admin/Units/Form',[
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
        $this->getView('Admin/Units/Delete', [
            'td' => $this->loader->getName(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * List action
     */
    public function listAction()
    {
        $this->getView('Admin/Units/List', [
            'td' => $this->loader->getName(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
