<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\Controller;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Exception\UnauthorizedException;
use ScoutUnitsList\Form\PositionForm;
use ScoutUnitsList\Model\Position;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\Tools\Paginator;

/**
 * Admin positions controller
 */
class PositionsController extends Controller
{
    /** @const string */
    const PAGE_NAME = 'sul-positions';

    /**
     * Routes
     */
    public function routes()
    {
        try {
            if (!current_user_can('sul_manage_positions')) {
                throw new UnauthorizedException();
            }
            
            $request = $this->request;
            $action = $request->query->getString('action', 'list');
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
                    $this->listAction($request);
                    break;
            }
        } catch (UnauthorizedException $e) {
            $this->respondWith401($e);
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
    public function formAction(Request $request, $id)
    {
        $positionRepository = $this->get('repository.position');
        $position = isset($id) ? $positionRepository->getOneByOr404([
                'id' => $id,
            ]) : new Position();

        $messageManager = $this->get('manager.message');

        $form = $this->createForm(PositionForm::class, $position, [
            'typesManager' => $this->get('manager.types'),
        ]);
        if ($form->isValid()) {
            try {
                // @TODO: set proper slug here instead of inside model - check if there is no duplication
                $positionRepository->save($position);
                if (!isset($id)) {
                    $form->setAction($request->getCurrentUrl([
                        'id' => $position->getId(),
                    ]));
                }
                $messageManager->addSuccess(__('Position was successfully saved.', 'scout-units-list'));
            } catch (Exception $e) {
                unlink($e);
                $messageManager->addError(__('An error occured during position saving.', 'scout-units-list'));
            }
        }

        $this->getView('Admin/Positions/Form', [
            'form' => $form,
            'messages' => $messageManager->getMessages(),
            'position' => $position,
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
        $positionRepository = $this->get('repository.position');
        $position = $positionRepository->getOneByOr404([
            'id' => $id,
        ]);

        $messageManager = $this->get('manager.message');

        $positionInUse = $this->get('repository.person')
            ->isPositionInUse($position);
        if ($positionInUse) {
            $messageManager->addError(__('This position is in use - you can\'t delete it.', 'scout-units-list'));
            return;
        }

        try {
            $positionRepository->delete($position);
            $messageManager->addSuccess(__('Position was successfully deleted.', 'scout-units-list'));
        } catch (Exception $e) {
            unset($e);
            $messageManager->addError(__('An error occured during position removing.', 'scout-units-list'));
        }
    }

    /**
     * List action
     *
     * @param Request $request request
     */
    public function listAction(Request $request)
    {
        $order = $this->getOrder();
        $page = max(1, $request->query->getInt('paged', 1));

        $positions = $this->get('repository.position')
            ->getPaginatorBy([], $order, 20, $page);

        $this->getView('Admin/Positions/List', [
            'messages' => $this->get('manager.message')
                ->getMessages(),
            'positions' => $positions,
            'typesManager' => $this->get('manager.types'),
        ])->setLinkData(AdminController::SCRIPT_NAME, self::PAGE_NAME)
            ->render();
    }

    /**
     * Get order
     *
     * @return array
     */
    private function getOrder()
    {
        $order = [
            'type' => Paginator::ORDER_DESC,
            'leader' => Paginator::ORDER_DESC,
        ];

        return $order;
    }
}
