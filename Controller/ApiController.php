<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Exception\UnauthorizedException;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\Tools\JsonTrait;

/**
 * API controller
 */
class ApiController extends Controller
{
    use JsonTrait;

    /**
     * Routes
     *
     * @param int $version version
     */
    public function routes($version)
    {
        try {
            $request = $this->request;
            $action = $request->query->getString('action', 'structure');

            switch ($action) {
                case 'structure':
                default:
                    $this->structureAction($request, $version);
                    break;
            }
        } catch (UnauthorizedException $e) {
            $this->respondWith401($e);
        } catch (NotFoundException $e) {
            $this->respondWith404($e);
        }
    }

    /**
     * Users action
     *
     * @param Request $request request
     * @param int     $version version
     */
    public function structureAction(Request $request, $version)
    {
        if ($version != 1) {
            throw new NotFoundException('Unknown API version.');
        }

        $id = $request->query->getInt('root');
        if ($id < 1) {
            throw new NotFoundException('Unproper root unit ID.');
        }

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('api-v' . $version . '-structure-' . $id);
        if (!$cacheManager->has()) {
            $unitRepository = $this->loader->get('repository.unit');
            $unit = $unitRepository->getOneBy([
                'id' => $id,
            ]);
            if (isset($unit)) {
                $unitRepository->loadDependentUnits($unit);
                $this->loader->get('repository.person')
                    ->setPersonsToUnits($unitRepository->getFlatUnitsList($unit),
                        $this->loader->get('repository.position'), $this->loader->get('repository.user'));
                $cacheManager->set(json_encode($unit));
            } else {
                $cacheManager->set('');
            }
        }

        $this->sendResponse($cacheManager->get());
    }
}
