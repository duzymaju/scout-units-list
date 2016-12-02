<?php

namespace ScoutUnitsList\Controller;

use ScoutUnitsList\System\ParamPack;

/**
 * Shortcodes controller
 */
class ShortcodesController extends Controller
{
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
        $currentUnit = $withCurrent ? $this->loader->get('repository.unit')
            ->getOneBy([
                'id' => $id,
            ]) : null;
        $dependentUnitsResult = $this->getDependentUnitsResult($id, $levels);

        $cacheManager = $this->get('manager.cache');
        $cacheManager->setId('units-list-' . $id . '-' . ($withCurrent ? '1' : '0') . '-' . $levels);
        if (!$cacheManager->has()) {
            $cacheManager->set($this->getView('Shortcodes/UnitsListMain', [
                'current' => $currentUnit,
                'dependents' => $dependentUnitsResult,
            ])->getRender());
        }

        return $cacheManager->get();
    }

    /**
     * Get dependent units result
     *
     * @param int $id     ID
     * @param int $levels levels
     *
     * @return string
     */
    private function getDependentUnitsResult($id, $levels)
    {
        $levels--;

        $units = $this->loader->get('repository.unit')
            ->getBy([
                'parentId' => $id,
            ]);
        $elements = [];
        foreach ($units as $unit) {
            $elements[] = [
                'current' => $unit,
                'dependents' => $levels > 0 ? $this->getDependentUnitsResult($unit->getId(), $levels) : '',
            ];
        }

        $result = $this->getView('Shortcodes/UnitsListLevel', [
            'elements' => $elements,
        ])->getRender();

        return $result;
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
