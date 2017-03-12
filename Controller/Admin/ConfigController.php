<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\Controller;
use ScoutUnitsList\Exception\UnauthorizedException;
use ScoutUnitsList\Form\ConfigForm;

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

            $this->formAction();
        } catch (UnauthorizedException $e) {
            $this->respondWith401($e);
        }
    }

    /**
     * Form action
     */
    public function formAction()
    {
        $configManager = $this->loader->get('manager.config');
        $config = $configManager->get();

        $messageManager = $this->get('manager.message');

        $form = $this->createForm(ConfigForm::class, $config, [
            'validator' => [
                'baseDir' => $this->loader->getPath() . '/',
            ],
        ]);
        if ($form->isValid()) {
            $configManager->save($config);
            $messageManager->addSuccess(__('Configuration was successfully saved.', 'scout-units-list'));
        }

        $this->getView('Admin/Config', [
            'config' => $config,
            'form' => $form,
            'messages' => $messageManager->getMessages(),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
