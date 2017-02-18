<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Unit;

/**
 * Unit repository
 */
class UnitRepository extends VersionedRepository
{
    /** @const string */
    const NAME = 'sul_units';

    /**
     * Get model
     *
     * @return string
     */
    protected function getModel()
    {
        return Unit::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this
            ->setStructureElement('id', DbManager::TYPE_DECIMAL, null, true)
            ->setStructureElement('type', DbManager::TYPE_STRING)
            ->setStructureElement('subtype', DbManager::TYPE_STRING)
            ->setStructureElement('sort', DbManager::TYPE_DECIMAL)
            ->setStructureElement('parentId', DbManager::TYPE_DECIMAL, 'parent_id')
            ->setStructureElement('orderId', DbManager::TYPE_DECIMAL, 'order_id')
            ->setStructureElement('orderNo', DbManager::TYPE_STRING, 'order_no')
            ->setStructureElement('slug', DbManager::TYPE_STRING)
            ->setStructureElement('name', DbManager::TYPE_STRING)
            ->setStructureElement('nameFull', DbManager::TYPE_STRING, 'name_full')
            ->setStructureElement('hero', DbManager::TYPE_STRING)
            ->setStructureElement('heroFull', DbManager::TYPE_STRING, 'hero_full')
            ->setStructureElement('url', DbManager::TYPE_STRING)
            ->setStructureElement('mail', DbManager::TYPE_STRING)
            ->setStructureElement('address', DbManager::TYPE_STRING)
            ->setStructureElement('meetingsTime', DbManager::TYPE_STRING, 'meetings_time')
            ->setStructureElement('localizationLat', DbManager::TYPE_FLOAT, 'localization_lat')
            ->setStructureElement('localizationLng', DbManager::TYPE_FLOAT, 'localization_lng')
        ;
        parent::defineStructure();
    }

    /**
     * Modify old version
     *
     * @param Unit $oldUnitVersion old unit version
     *
     * @return Unit
     */
    protected function modifyOldVersion(Unit $oldUnitVersion)
    {
        $oldUnitVersion->setSlug(null);

        return $oldUnitVersion;
    }

    /**
     * Find by name and types
     *
     * @param string $name  name
     * @param array  $types types
     * @param int    $limit limit
     *
     * @return array
     */
    public function findByNameAndTypes($name, array $types = null, $limit = 10)
    {
        $statement = $this->db->prepare('SELECT * FROM `' . $this->getTableName() . '` ' .
                'WHERE (`name` LIKE :name || `name_full` LIKE :name) && `group_id` = 0 && `action` IN (:actions)' .
                (isset($types) ? ' && `type` IN (:types)' : '') . ' LIMIT ' . ((int) $limit))
            ->setParam('name', '%' . $this->escapeLike($name) . '%')
            ->setParam('actions', $this->getActionsExceptRemoved());
        if (isset($types)) {
            $statement->setParam('types', $types);
        }
        $query = $statement->getQuery();
        $results = $this->db->getResults($query, ARRAY_A);

        $items = [];
        foreach ($results as $result) {
            $items[] = $this->createModel($result);
        }

        return $items;
    }

    /**
     * Get unique slug
     * 
     * @param Unit $unit unit
     *
     * @return string
     */
    public function getUniqueSlug(Unit $unit)
    {
        $slug = $unit->getSlug();
        $query = $this->db->prepare('SELECT `slug` FROM `' . $this->getTableName() . '` WHERE `slug` LIKE :slug')
            ->setParam('slug', $this->escapeLike($slug) . '%')
            ->getQuery();
        $results = $this->db->getResults($query, ARRAY_A);

        $slugs = [];
        foreach ($results as $result) {
            $slugs[] = $result['slug'];
        }

        $i = 1;
        while (in_array($slug, $slugs)) {
            $i++;
            $suffix = '-' . $i;
            $maxlength = 50 - strlen($suffix);
            $slug = substr($unit->getSlug(), 0, $maxlength) . $suffix;
        }

        return $slug;
    }

    /**
     * Load dependent units
     *
     * @param Unit     $parent parent
     * @param int|null $levels levels
     *
     * @return Unit
     */
    public function loadDependentUnits(Unit $parent, $levels = null, array $types = null)
    {
        if (isset($levels)) {
            $levels--;
        }
        $conditions = [
            'parentId' => $parent->getId(),
        ];
        if (isset($types)) {
            $conditions['type'] = $types;
        }

        foreach ($this->getBy($conditions) as $child) {
            if ($child->getId() != $parent->getId()) {
                $child->setParent($parent);
                $parent->addChild($child);
                if (!isset($levels) || $levels > 0) {
                    $this->loadDependentUnits($child, $levels, $types);
                }
            }
        }

        return $parent;
    }
    
    /**
     * Get flat units list
     * 
     * @param Unit $parent    parent
     * @param bool $idsAsKeys IDs as keys
     * 
     * @return Unit[]
     */
    public function getFlatUnitsList(Unit $parent, $idsAsKeys = false)
    {
        $list = [
            $parent->getId() => $parent,
        ];
        foreach ($parent->getChildren() as $child) {
            if ($child->getId() != $parent->getId()) {
                foreach ($this->getFlatUnitsList($child, true) as $descendant) {
                    if (!array_key_exists($descendant->getId(), $list)) {
                        $list[$descendant->getId()] = $descendant;
                    }
                }
            }
        }

        if (!$idsAsKeys) {
            $list = array_values($list);
        }

        return $list;
    }
}
