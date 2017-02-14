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
}
