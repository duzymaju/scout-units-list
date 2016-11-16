<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Controller\Admin\ConfigController;
use ScoutUnitsList\Controller\Admin\PositionsController;
use ScoutUnitsList\Controller\Admin\UnitsController;

/**
 * Admin controller
 */
class AdminController extends BasicController
{
    /** @const string */
    const SCRIPT_NAME = 'admin.php';

    /**
     * Menu
     */
    public function menu()
    {
        $textDomain = $this->loader->getName();

        $unitsController = new UnitsController($this->request, $this->loader);
        $positionsController = new PositionsController($this->request, $this->loader);
        $configController = new ConfigController($this->request, $this->loader);

        add_menu_page(__('Scout units list', $textDomain), __('Scout units', $textDomain), 'manage_categories', $unitsController::PAGE_NAME, [
            $unitsController,
            'routes',
        ], '', 3);
        add_submenu_page($unitsController::PAGE_NAME, __('Scout units list', $textDomain), __('Units list', $textDomain), 'manage_categories', $unitsController::PAGE_NAME, [
            $unitsController,
            'routes',
        ]);
        add_submenu_page($unitsController::PAGE_NAME, __('Scout positions list', $textDomain), __('Positions list', $textDomain), 'manage_categories', $positionsController::PAGE_NAME, [
            $positionsController,
            'routes',
        ]);
        add_submenu_page($unitsController::PAGE_NAME, __('Scout units configuration', $textDomain), __('Configuration', $textDomain), 'manage_categories', $configController::PAGE_NAME, [
            $configController,
            'routes',
        ]);
    }
}
