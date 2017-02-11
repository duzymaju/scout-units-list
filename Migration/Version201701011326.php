<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;
use ScoutUnitsList\Model\Unit;

/**
 * Version 2017-01-01 13:26
 */
class Version201701011326 extends Version
{
    /**
     * Up
     */
    public function up()
    {
        $personName = PersonRepository::NAME;
        $positionName = PositionRepository::NAME;
        $unitName = UnitRepository::NAME;

        $this
            ->addSql('
                CREATE TABLE IF NOT EXISTS `' . $this->getTableName($positionName) . '` (
                    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `type` char(1) COLLATE utf8_polish_ci NOT NULL,
                    `name_male` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                    `name_female` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                    `description` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
                    `leader` bool NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`),
                    INDEX `' . $this->getOldIndexName($positionName, 1) . '` (`type`),
                    INDEX `' . $this->getOldIndexName($positionName, 2) . '` (`leader`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
            ')
            ->addSql('
                CREATE TABLE IF NOT EXISTS `' . $this->getTableName($unitName) . '` (
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
                    INDEX `' . $this->getOldIndexName($unitName, 1) . '` (`status`),
                    INDEX `' . $this->getOldIndexName($unitName, 2) . '` (`type`),
                    INDEX `' . $this->getOldIndexName($unitName, 3) . '` (`subtype`),
                    INDEX `' . $this->getOldIndexName($unitName, 4) . '` (`parent_id`),
                    UNIQUE `' . $this->getOldIndexName($unitName, 5) . '` (`slug`),
                    FOREIGN KEY (`parent_id`)
                        REFERENCES `' . $this->getTableName($unitName) . '` (`id`)
                        ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
            ')
            ->addSql('
                CREATE TABLE IF NOT EXISTS `' . $this->getTableName($personName) . '` (
                    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `user_id` bigint(20) UNSIGNED NOT NULL,
                    `unit_id` int(10) UNSIGNED NOT NULL,
                    `position_id` int(10) UNSIGNED NOT NULL,
                    `order_no` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE `' . $this->getOldIndexName($personName, 1) . '` (`user_id`, `unit_id`, `position_id`),
                    FOREIGN KEY (`user_id`)
                        REFERENCES `' . $this->getTableName('users') . '` (`ID`)
                        ON DELETE CASCADE,
                    FOREIGN KEY (`unit_id`)
                        REFERENCES `' . $this->getTableName($unitName) . '` (`id`)
                        ON DELETE CASCADE,
                    FOREIGN KEY (`position_id`)
                        REFERENCES `' . $this->getTableName($positionName) . '` (`id`)
                        ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
            ')
        ;
    }

    /**
     * Down
     */
    public function down()
    {
        $this
            ->addSql('
                DROP TABLE IF EXISTS `' . $this->getTableName(PersonRepository::NAME) . '`;
            ')
            ->addSql('
                DROP TABLE IF EXISTS `' . $this->getTableName(UnitRepository::NAME) . '`;
            ')
            ->addSql('
                DROP TABLE IF EXISTS `' . $this->getTableName(PositionRepository::NAME) . '`;
            ')
        ;
    }

    /**
     * Get old index name
     *
     * @param string $name name
     * @param int    $no   no
     *
     * @return string
     */
    private function getOldIndexName($name, $no)
    {
        $prefix = 'sul_';
        $indexName = $this->getIndexName($name, $no);
        $oldIndexName = strpos($indexName, $prefix) === 0 ? substr($indexName, strlen($prefix)) : $indexName;

        return $oldIndexName;
    }
}
