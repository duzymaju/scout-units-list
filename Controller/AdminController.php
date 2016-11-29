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
        $unitsController = new UnitsController($this->loader, $this->request);
        $positionsController = new PositionsController($this->loader, $this->request);
        $configController = new ConfigController($this->loader, $this->request);

        add_menu_page(__('Scout units list', 'scout-units-list'), __('Scout units', 'scout-units-list'),
            'sul_modify_own_units', $unitsController::PAGE_NAME, [
                $unitsController,
                'routes',
            ], '', 3);
        add_submenu_page($unitsController::PAGE_NAME, __('Scout units list', 'scout-units-list'),
            __('Units list', 'scout-units-list'), 'sul_modify_own_units', $unitsController::PAGE_NAME, [
                $unitsController,
                'routes',
            ]);
        add_submenu_page($unitsController::PAGE_NAME, __('Scout positions list', 'scout-units-list'),
            __('Positions list', 'scout-units-list'), 'sul_manage_positions', $positionsController::PAGE_NAME, [
                $positionsController,
                'routes',
            ]);
        add_submenu_page($unitsController::PAGE_NAME, __('Scout units configuration', 'scout-units-list'),
            __('Configuration', 'scout-units-list'), 'sul_manage_config', $configController::PAGE_NAME, [
                $configController,
                'routes',
            ]);
    }
}
