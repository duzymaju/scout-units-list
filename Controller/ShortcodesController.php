<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Form\UnitAdminForm;
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
        $levels = max(0, $attributes->getInt('levels', 1));
        $typesList = $attributes->getString('types');
        if (empty($typesList)) {
            $types = null;
        } else {
            $allowedTypes = array_keys(UnitAdminForm::getTypes());
            $types = [];
            foreach (explode(',', $typesList) as $type) {
                if (!in_array($type, $types) && in_array($type, $allowedTypes)) {
                    $types[] = $type;
                }
            }
        }
        $cssClass = $attributes->getString('class');

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('units-list-' . $id . '-' . ($withCurrent ? '1' : '0') . '-' . $levels .
            (isset($types) ? '-' . implode(',', $types) : ''));
        if (!$cacheManager->has()) {
            $unitRepository = $this->loader->get('repository.unit');
            $unit = $withCurrent ? $unitRepository->getOneBy([
                'id' => $id,
            ]) : null;
            if (isset($unit)) {
                $unitRepository->loadDependentUnits($unit, $levels, $types);
                $this->setPersonsToUnits($unitRepository->getFlatUnitsList($unit), true);
                $dependentUnitsResult = $this->getDependentUnitsResult($unit, $unit, $levels, $types);

                $cacheManager->set($this->getRenderedView([
                    'UnitsList-' . $unit->getType(),
                    'UnitsList-' . $unit->getId(),
                    'UnitsList',
                ], 'UnitsList', [
                    'cssClass' => $cssClass,
                    'current' => $unit,
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

        $cssClass = $attributes->getString('class');

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('persons-list-' . $id);
        if (!$cacheManager->has()) {
            $unit = $this->loader->get('repository.unit')
                ->getOneBy([
                    'id' => $id,
                ]);
            if (isset($unit)) {
                $this->setPersonsToUnits([
                    $unit,
                ], true, false);

                $cacheManager->set($this->getRenderedView([
                    'PersonsList-' . $unit->getType(),
                    'PersonsList-' . $unit->getId(),
                    'PersonsList',
                ], 'PersonsList', [
                    'cssClass' => $cssClass,
                    'unit' => $unit,
                ]));
            } else {
                $cacheManager->set('');
            }
        }

        return $cacheManager->get();
    }

    /**
     * Get dependent units result
     *
     * @param Unit       $root         root
     * @param Unit       $parent       parent
     * @param int        $levels       levels
     * @param array|null $types        types
     * @param int        $currentLevel current level
     *
     * @return string
     */
    private function getDependentUnitsResult(Unit $root, Unit $parent, $levels, array $types = null, $currentLevel = 1)
    {
        $levels--;
        $nextLevel = $currentLevel + 1;
        $elements = [];
        foreach ($parent->getChildren() as $child) {
            $elements[] = [
                'current' => $child,
                'dependents' => $levels > 0 ?
                    $this->getDependentUnitsResult($root, $child, $levels, $types, $nextLevel) : '',
            ];
        }

        $result = $this->getRenderedView([
            'UnitsListLevel-' . $root->getType(),
            'UnitsListLevel-' . $root->getId(),
            'UnitsListLevel',
        ], 'UnitsListLevel', [
            'elements' => $elements,
            'level' => $currentLevel,
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
        return $this->loader->get('repository.person')
            ->setPersonsToUnits($units, $this->loader->get('repository.position'),
                $includeUsers ? $this->loader->get('repository.user') : null, null, $leaderOnly);
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

        $attributesList = array_shift($arguments);
        $attributes = new ParamPack(is_array($attributesList) ? $attributesList : []);
        $content = array_shift($arguments);
        $result = call_user_func([
            $this,
            $method,
        ], $attributes, $content);

        return $result;
    }
}
