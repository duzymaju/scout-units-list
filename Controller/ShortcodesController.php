<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\View;

/**
 * Shortcodes controller
 */
class ShortcodesController extends Controller
{
    /** @var array */
    protected $units;

    /**
     * Units list
     *
     * @param ParamPack $attributes attributes
     *
     * @return string
     */
    public function unitsListShortcode(ParamPack $attributes)
    {
        $id = $attributes->getInt('id');
        if ($id < 1) {
            return '';
        }

        $withCurrent = $attributes->getBool('current', false);
        $levels = $attributes->getInt('levels', 1);

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('units-list-' . $id . '-' . ($withCurrent ? '1' : '0') . '-' . $levels);
        if (!$cacheManager->has()) {
            $currentUnit = $withCurrent ? $this->loader->get('repository.unit')
                ->getOneBy([
                    'id' => $id,
                    'status' => Unit::STATUS_ACTIVE,
                ]) : null;
            if (isset($currentUnit)) {
                $this->units = [
                    $currentUnit,
                ];
                $dependentUnitsResult = $this->getDependentUnitsResult($id, $id, $levels);
                $this->setPersonsToUnits($this->units, true);

                $cacheManager->set($this->getRenderedView([
                    'UnitsList-' . $id,
                    'UnitsList',
                ], 'UnitsList', [
                    'current' => $currentUnit,
                    'dependents' => $dependentUnitsResult,
                ]));
            } else {
                $cacheManager->set('');
            }
        }

        return $cacheManager->get();
    }

    /**
     * Persons list
     *
     * @param ParamPack $attributes attributes
     *
     * @return string
     */
    public function personsListShortcode(ParamPack $attributes)
    {
        $id = $attributes->getInt('id');
        if ($id < 1) {
            return '';
        }

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('persons-list-' . $id);
        if (!$cacheManager->has()) {
            $unit = $this->loader->get('repository.unit')
                ->getOneBy([
                    'id' => $id,
                    'status' => Unit::STATUS_ACTIVE,
                ]);
            if (isset($unit)) {
                $this->setPersonsToUnits([
                    $unit,
                ], true, false);
            }

            $cacheManager->set($this->getRenderedView([
                'PersonsList-' . $id,
                'PersonsList',
            ], 'PersonsList', [
                'unit' => $unit,
            ]));
        }

        return $cacheManager->get();
    }

    /**
     * Get dependent units result
     *
     * @param int $rootId root ID
     * @param int $id     ID
     * @param int $levels levels
     *
     * @return string
     */
    private function getDependentUnitsResult($rootId, $id, $levels)
    {
        $levels--;

        $units = $this->loader->get('repository.unit')
            ->getBy([
                'parentId' => $id,
                'status' => Unit::STATUS_ACTIVE,
            ]);
        $elements = [];
        foreach ($units as $unit) {
            $this->units[] = $unit;
            $elements[] = [
                'current' => $unit,
                'dependents' => $levels > 0 ? $this->getDependentUnitsResult($rootId, $unit->getId(), $levels) : '',
            ];
        }

        $result = $this->getRenderedView([
            'UnitsListLevel',
            'UnitsListLevel-' . $rootId,
        ], 'UnitsListLevel', [
            'elements' => $elements,
        ]);

        return $result;
    }

    /**
     * Set persons to units
     *
     * @param Unit[] $units        units
     * @param bool   $includeUsers include users
     * @param bool   $leaderOnly   leader only
     *
     * @return self
     */
    private function setPersonsToUnits(array $units, $includeUsers = false, $leaderOnly = true)
    {
        return $this->loader->get('repository.person')->setPersonsToUnits($units,
            $this->loader->get('repository.position'), $includeUsers ? $this->loader->get('repository.user') : null,
            null, $leaderOnly);
    }

    /**
     * Get rendered view
     *
     * @param array  $customizedNames customized names
     * @param string $name            name
     * @param array  $params          parameters
     *
     * @return string
     */
    private function getRenderedView(array $customizedNames, $name, array $params = [])
    {
        $customizedPath = get_template_directory() . '/scout-units-list';
        $customizedParams = array_merge($params, [
            'path' => $customizedPath,
        ]);
        foreach ($customizedNames as $customizedName) {
            if (file_exists($customizedPath . '/' . $customizedName . View::TEMPLATE_EXT)) {
                return $this->getView($customizedName, $customizedParams)
                    ->getRender();
            }
        }

        return $this->getView('Shortcodes/' . $name, $params)
            ->getRender();
    }

    /**
     * Call
     *
     * @param string $name      name
     * @param array  $arguments arguments
     *
     * @return string
     */
    public function __call($name, array $arguments)
    {
        $method = $name . 'Shortcode';
        if (!method_exists($this, $method)) {
            return '';
        }

        $attributes = new ParamPack(array_shift($arguments));
        $content = array_shift($arguments);
        $result = call_user_func([
            $this,
            $method,
        ], $attributes, $content);

        return $result;
    }
}
