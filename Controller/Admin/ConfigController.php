<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;

/**
 * Admin config controller
 */
class ConfigController extends BasicController
{
    /** @const string */
    const PAGE_NAME = 'sul-config';

    /**
     * Routes
     */
    public function routes()
    {
        $this->formAction();
    }

    /**
     * Edit action
     */
    public function formAction()
    {
        $options = $this->loader->get('manager.config')
            ->getAll();

        $this->getView('Admin/Config', [
            'options' => $options,
            'td' => $this->loader->getName(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
