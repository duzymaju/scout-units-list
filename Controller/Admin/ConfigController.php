<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\Controller;
use ScoutUnitsList\Exception\UnauthorizedException;
use ScoutUnitsList\Form\ConfigForm;
use ScoutUnitsList\System\Request;

/**
 * Admin config controller
 */
class ConfigController extends Controller
{
    /** @const string */
    const PAGE_NAME = 'sul-config';

    /**
     * Routes
     */
    public function routes()
    {
        try {
            if (!current_user_can('sul_manage_config')) {
                throw new UnauthorizedException();
            }

            $this->formAction($this->request);
        } catch (UnauthorizedException $e) {
            $this->respondWith401($e);
        }
    }

    /**
     * Edit action
     */
    public function formAction(Request $request)
    {
        $configManager = $this->loader->get('manager.config');
        $config = $configManager->get();

        $messageManager = $this->get('manager.message');

        $form = new ConfigForm($request, $config);
        if ($form->isValid()) {
            $configManager->save($config);
            $messageManager->addSuccess(__('Position was successfully saved.', 'scout-units-list'));
        }

        $this->getView('Admin/Config', [
            'config' => $config,
            'form' => $form,
            'messages' => $messageManager->getMessages(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
