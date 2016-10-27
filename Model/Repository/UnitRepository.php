<?php

namespace ScoutUnitsList\Model\Repository;

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
     * Get map
     *
     * @return array
     */
    protected static function getMap()
    {
        $map = [
            'id' => 'id',
            'status' => 'status',
            'type' => 'type',
            'subtype' => 'subtype',
            'sort' => 'sort',
            'parent_id' => 'parentId',
            'slug' => 'slug',
            'name' => 'name',
            'name_full' => 'nameFull',
            'hero' => 'hero',
            'hero_full' => 'heroFull',
            'url' => 'url',
            'mail' => 'mail',
            'address' => 'address',
            'meetings_time' => 'meetingsTime',
            'localization_lat' => 'localizationLat',
            'localization_lng' => 'localizationLng',
        ];

        return $map;
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
                `slug` varchar(30) COLLATE utf8_polish_ci NOT NULL,
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
}
