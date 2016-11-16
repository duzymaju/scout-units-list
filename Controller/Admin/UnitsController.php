<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\System\Request;

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
    public function formAction(Request $request, $id = null)
    {
        $unitRepository = $this->get('repository.unit');
        $unit = $id > 0 ? $unitRepository->getOneByOr404(array(
                'id' => $id,
            )) : null;

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        if ($request->isPost()) {
            if (!isset($unit)) {
                $unit = new Unit();
            }
            $unit->setStatus($request->request->getString('status'))
                ->setType($request->request->getString('type'))
                ->setSubtype($request->request->getString('subtype'))
                ->setSort($request->request->getString('sort'))
                ->setParentId($request->request->getInt('parentId'))
                ->setName($request->request->getString('name'))
                ->setNameFull($request->request->getString('nameFull'))
                ->setHero($request->request->getString('hero'))
                ->setHeroFull($request->request->getString('heroFull'))
                ->setUrl($request->request->getString('url'))
                ->setMail($request->request->getString('mail'))
                ->setAddress($request->request->getString('address'))
                ->setMeetingsTime($request->request->getString('meetingsTime'))
                ->setLocalizationLat($request->request->getFloat('localizationLat'))
                ->setLocalizationLng($request->request->getFloat('localizationLng'));
            try {
                $unitRepository->save($unit);
                $messageManager->addSuccess(__('Unit was successfully saved.', $td));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during unit saving.', $td));
            }
        }

        $this->getView('Admin/Units/Form', array(
            'messages' => $messageManager->getMessages(),
            'statuses' => Unit::getStatuses(),
            'subtypes' => Unit::getSubtypes(),
            'td' => $this->loader->getName(),
            'types' => Unit::getTypes(),
            'unit' => $unit,
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
        $unitRepository = $this->get('repository.unit');
        $unit = $unitRepository->getOneByOr404(array(
            'id' => $id,
        ));

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        try {
            $unitRepository->delete($unit);
            $messageManager->addSuccess(__('Unit was successfully deleted.', $td));
        } catch (Exception $e) {
            unset($e);
            $messageManager->addError(__('An error occured during unit removing.', $td));
        }
    }

    /**
     * List action
     */
    public function listAction()
    {
        $units = $this->get('repository.unit')
            ->getBy(array());

        $this->getView('Admin/Units/List', array(
            'messages' => $this->get('manager.message')
                ->getMessages(),
            'td' => $this->loader->getName(),
            'units' => $units,
        ))->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }
}
