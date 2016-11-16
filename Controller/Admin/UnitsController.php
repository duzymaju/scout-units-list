<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\BasicController;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Model\Unit;

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

        try {
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
        } catch (NotFoundException $e) {
            $this->respondWith404($e);
        }
    }

    /**
     * Form action
     *
     * @param int|null $id ID
     */
    public function formAction($id = null)
    {
        $unitRepository = $this->get('repository.unit');
        $unit = $id > 0 ? $unitRepository->getOneByOr404(array(
                'id' => $id,
            )) : null;

        $td = $this->loader->getName();
        $messageManager = $this->get('manager.message');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($unit)) {
                $unit = new Unit();
            }
            $unit->setStatus($_POST['status'])
                ->setType($_POST['type'])
                ->setSubtype(empty($_POST['subtype']) ? null : $_POST['subtype'])
                ->setSort($_POST['sort'])
                ->setParentId(empty($_POST['parentId']) ? null : $_POST['parentId'])
                ->setName($_POST['name'])
                ->setNameFull(empty($_POST['nameFull']) ? null : $_POST['nameFull'])
                ->setHero(empty($_POST['hero']) ? null : $_POST['hero'])
                ->setHeroFull(empty($_POST['heroFull']) ? null : $_POST['heroFull'])
                ->setUrl(empty($_POST['url']) ? null : $_POST['url'])
                ->setMail(empty($_POST['mail']) ? null : $_POST['mail'])
                ->setAddress(empty($_POST['address']) ? null : $_POST['address'])
                ->setMeetingsTime(empty($_POST['meetingsTime']) ? null : $_POST['meetingsTime'])
                ->setLocalizationLat(empty($_POST['localizationLat']) ? null : $_POST['localizationLat'])
                ->setLocalizationLng(empty($_POST['localizationLng']) ? null : $_POST['localizationLng']);
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
