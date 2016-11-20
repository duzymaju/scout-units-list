<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;
use ScoutUnitsList\Form\ConfigForm;
use ScoutUnitsList\System\Request;

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
        $this->formAction($this->request);
    }

    /**
     * Edit action
     */
    public function formAction(Request $request)
    {
        $configManager = $this->loader->get('manager.config');
        $config = $configManager->get();

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        $form = new ConfigForm($request, $config);
        if ($form->isValid()) {
            $configManager->save($config);
            $messageManager->addSuccess(__('Position was successfully saved.', $td));
        }

        $this->getView('Admin/Config', [
            'config' => $config,
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'td' => $td,
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
