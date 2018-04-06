<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\Form\UnitAdminForm;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\Request;
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
        if ($attributes->getInt('id') < 1) {
            return '';
        }

        $cache = $this->getUnitCache('list', $attributes,
            function (Unit $unit, $withCurrent, $cssClass, $levels, $types) {
                return $this->getRenderedView([
                    'UnitsList-' . $unit->getType(),
                    'UnitsList-' . $unit->getId(),
                    'UnitsList',
                ], 'UnitsList', [
                    'cssClass' => $cssClass,
                    'current' => $unit,
                    'dependents' => $this->getDependentUnitsResult($unit, $unit, $levels, $types),
                    'withCurrent' => $withCurrent,
                ]);
            }
        );

        return $cache;
    }

    /**
     * Get unit cache
     *
     * @param string    $cachePrefix  cache prefix
     * @param ParamPack $attributes   attributes
     * @param callable  $dataForCache data for cache
     *
     * @return string
     */
    private function getUnitCache($cachePrefix, ParamPack $attributes, callable $dataForCache)
    {
        $id = $attributes->getInt('id');
        $withCurrent = $attributes->getBool('current', false);
        $levels = max(0, $attributes->getInt('levels', 1));
        $typesList = $attributes->getString('types');
        $isExternal = $attributes->getBool('external', false);
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

        $cacheIdParts = [
            'units',
            $cachePrefix,
            $id,
            $withCurrent ? '1' : '0',
            $levels,
        ];
        if (isset($types)) {
            $cacheIdParts[] = implode(',', $types);
        }
        $cacheIdParts[] = $isExternal ? '1' : '0';
        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId(implode('-', $cacheIdParts));

        if (!$cacheManager->has()) {
            $unit = $this->getUnitWithDependencies($id, $levels, $types, $isExternal);
            $data = isset($unit) ? $dataForCache($unit, $withCurrent, $cssClass, $levels, $types) : '';
            $cacheManager->set($data);
        }
        $data = $cacheManager->get();

        return $data;
    }

    /**
     * Get unit with dependencies
     *
     * @param int        $id         ID
     * @param int        $levels     levels
     * @param array|null $types      types
     * @param bool       $isExternal if is external
     *
     * @return Unit|null
     */
    private function getUnitWithDependencies($id, $levels, $types, $isExternal)
    {
        if ($isExternal) {
            $unit = $this->getExternalUnit($id);
        } else {
            $unitRepository = $this->loader->get('repository.unit');
            $unit = $unitRepository->getOneBy([
                'id' => $id,
            ]);
            if (!isset($unit)) {
                return null;
            }

            $unitRepository->loadDependentUnits($unit, $levels, $types);
            $this->setPersonsToUnits($unitRepository->getFlatUnitsList($unit), true);
        }

        return $unit;
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
        $isExternal = $attributes->getString('external', false);

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('persons-list-' . $id . '-' . ($isExternal ? '1' : '0'));
        if (!$cacheManager->has()) {
            $unit = $this->getUnitWithPersons($id, $isExternal);
            if (isset($unit)) {
                $cacheManager->set($this->getRenderedView([
                    'PersonsList-' . $unit->getType(),
                    'PersonsList-' . $unit->getId(),
                    'PersonsList',
                ], 'PersonsList', [
                    'config' => $this->get('manager.config')
                        ->get(),
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
     * Get unit with persons
     *
     * @param int  $id         ID
     * @param bool $isExternal if is external
     *
     * @return Unit|null
     */
    private function getUnitWithPersons($id, $isExternal)
    {
        if ($isExternal) {
            $unit = $this->getExternalUnit($id);
        } else {
            $unitRepository = $this->loader->get('repository.unit');
            $unit = $unitRepository->getOneBy([
                'id' => $id,
            ]);
            if (!isset($unit)) {
                return null;
            }

            $this->setPersonsToUnits([
                $unit,
            ], true, false);
        }

        return $unit;
    }

    /**
     * Get external unit
     *
     * @param int $id ID
     *
     * @return Unit|null
     */
    private function getExternalUnit($id)
    {
        /** @var Config $config */
        $config = $this->get('manager.config')
            ->get();
        if (!$config->hasExternalStructureUrl()) {
            return null;
        }

        $request = new Request();
        $json = $request->getContent(rtrim($config->getExternalStructureUrl(), '/') . '/index.php', [
            'action' => 'structure',
            'root' => $id,
            'sul_api_v' => 1,
        ]);
        if ($json === false) {
            return null;
        }

        $structure = json_decode($json);
        if (!isset($structure)) {
            return null;
        }
        $unit = Unit::create($structure);

        return $unit;
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
        /** @var Config $config */
        $config = $this->get('manager.config')
            ->get();

        $customizedPath = $config->hasShortcodeTemplatesPath() ?
            $this->loader->getPath($config->getShortcodeTemplatesPath()) :
            get_template_directory() . '/scout-units-list';
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
