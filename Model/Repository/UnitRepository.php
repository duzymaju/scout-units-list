<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Unit;

/**
 * Unit repository
 */
class UnitRepository extends BasicRepository
{
    /**
     * Get name
     *
     * @return string
     */
    protected static function getName()
    {
        return 'units';
    }

    /**
     * Get model
     *
     * @return string
     */
    protected static function getModel()
    {
        return Unit::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this->setStructureElement('id', DbManager::TYPE_DECIMAL, null, true)
            ->setStructureElement('status', DbManager::TYPE_STRING)
            ->setStructureElement('type', DbManager::TYPE_STRING)
            ->setStructureElement('subtype', DbManager::TYPE_STRING)
            ->setStructureElement('sort', DbManager::TYPE_DECIMAL)
            ->setStructureElement('parentId', DbManager::TYPE_DECIMAL, 'parent_id')
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
            ->setStructureElement('localizationLng', DbManager::TYPE_FLOAT, 'localization_lng');
    }

    /**
     * Install
     *
     * @return self
     */
    public function install()
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `' . $this->getPluginTableName() . '` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `status` char(1) COLLATE utf8_polish_ci NOT NULL DEFAULT "' . Unit::STATUS_ACTIVE . '",
                `type` char(1) COLLATE utf8_polish_ci NOT NULL DEFAULT "' . Unit::TYPE_TROOP . '",
                `subtype` char(1) COLLATE utf8_polish_ci DEFAULT NULL,
                `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
                `parent_id` int(10) UNSIGNED DEFAULT NULL,
                `order_no` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                `slug` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                `name_full` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
                `hero` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
                `hero_full` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
                `url` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
                `mail` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
                `address` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
                `meetings_time` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
                `localization_lat` float(10,6) DEFAULT NULL,
                `localization_lng` float(10,6) DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `' . $this->getIndexName(1) . '` (`status`),
                INDEX `' . $this->getIndexName(2) . '` (`type`),
                INDEX `' . $this->getIndexName(3) . '` (`subtype`),
                INDEX `' . $this->getIndexName(4) . '` (`parent_id`),
                UNIQUE `' . $this->getIndexName(5) . '` (`slug`),
                FOREIGN KEY (parent_id)
                    REFERENCES `' . $this->getPluginTableName() . '` (`id`)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
        ');

        return $this;
    }

    /**
     * Uninstall
     *
     * @return self
     */
    public function uninstall()
    {
        $this->db->query('
            DROP TABLE IF EXISTS `' . $this->getPluginTableName() . '`;
        ');

        return $this;
    }

    /**
     * Find by name
     *
     * @param string $name  name
     * @param int    $limit limit
     *
     * @return array
     */
    public function findByName($name, $limit = 10)
    {
        $query = $this->db->prepare('SELECT * FROM `' . $this->getPluginTableName() . '` ' .
                'WHERE `name` LIKE :name || `name_full` LIKE :name LIMIT ' . ((int) $limit))
            ->setParam('name', '%' . $this->escapeLike($name) . '%')
            ->getQuery();
        $results = $this->db->getResults($query, ARRAY_A);

        $items = [];
        foreach ($results as $result) {
            $items[] = $this->createObject($result);
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
        $query = $this->db->prepare('SELECT slug FROM `' . $this->getPluginTableName() . '` WHERE `slug` LIKE :slug')
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
