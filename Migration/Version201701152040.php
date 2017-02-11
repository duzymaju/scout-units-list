<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;
use ScoutUnitsList\Model\VersionedModelInterface;

/**
 * Version 2017-01-15 20:40
 */
class Version201701152040 extends Version
{
    /**
     * Up
     */
    public function up()
    {
        $this
            ->addSql('
                ALTER TABLE `' . $this->getTableName(PositionRepository::NAME) . '`
                    DROP INDEX `' . $this->getOldIndexName(PositionRepository::NAME, 1) . '`,
                    DROP INDEX `' . $this->getOldIndexName(PositionRepository::NAME, 2) . '`,
                    ADD INDEX `' . $this->getIndexName(PositionRepository::NAME, 1) . '` (`type`),
                    ADD INDEX `' . $this->getIndexName(PositionRepository::NAME, 2) . '` (`leader`);
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName(PersonRepository::NAME) . '`
                    ADD `order_id` INT UNSIGNED NULL DEFAULT NULL AFTER `position_id`,
                    ADD `group_id` INT UNSIGNED NOT NULL DEFAULT "0" AFTER `order_no`,
                    ADD `action` CHAR(1) COLLATE utf8_polish_ci NOT NULL
                        DEFAULT "' . VersionedModelInterface::ACTION_INSERTED . '" AFTER `group_id`,
                    ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `action`,
                    DROP INDEX `' . $this->getOldIndexName(PersonRepository::NAME, 1) . '`,
                    ADD UNIQUE `' . $this->getIndexName(PersonRepository::NAME, 1) . '`
                        (`user_id`, `unit_id`, `position_id`, `created_at`),
                    ADD INDEX `' . $this->getIndexName(PersonRepository::NAME, 2) . '` (`group_id`),
                    ADD INDEX `' . $this->getIndexName(PersonRepository::NAME, 3) . '` (`action`);
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName(UnitRepository::NAME) . '`
                    ADD `order_id` INT UNSIGNED NULL DEFAULT NULL AFTER `parent_id`,
                    CHANGE `slug` `slug` VARCHAR(50) COLLATE utf8_polish_ci NULL DEFAULT NULL,
                    ADD `group_id` INT UNSIGNED NOT NULL DEFAULT "0" AFTER `localization_lng`,
                    ADD `action` CHAR(1) COLLATE utf8_polish_ci NOT NULL
                        DEFAULT "' . VersionedModelInterface::ACTION_INSERTED . '" AFTER `group_id`,
                    ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `action`,
                    DROP INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 1) . '`,
                    DROP INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 2) . '`,
                    DROP INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 3) . '`,
                    DROP INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 4) . '`,
                    DROP INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 5) . '`,
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 1) . '` (`status`),
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 2) . '` (`type`),
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 3) . '` (`subtype`),
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 4) . '` (`parent_id`),
                    ADD UNIQUE `' . $this->getIndexName(UnitRepository::NAME, 5) . '` (`slug`),
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 6) . '` (`group_id`),
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 7) . '` (`action`);
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
                ALTER TABLE `' . $this->getTableName(UnitRepository::NAME) . '`
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 1) . '`,
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 2) . '`,
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 3) . '`,
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 4) . '`,
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 5) . '`,
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 6) . '`,
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 7) . '`,
                    ADD INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 1) . '` (`status`),
                    ADD INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 2) . '` (`type`),
                    ADD INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 3) . '` (`subtype`),
                    ADD INDEX `' . $this->getOldIndexName(UnitRepository::NAME, 4) . '` (`parent_id`),
                    ADD UNIQUE `' . $this->getOldIndexName(UnitRepository::NAME, 5) . '` (`slug`),
                    DROP `order_id`,
                    CHANGE `slug` `slug` VARCHAR(50) COLLATE utf8_polish_ci NOT NULL,
                    DROP `group_id`,
                    DROP `action`,
                    DROP `created_at`;
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName(PersonRepository::NAME) . '`
                    DROP INDEX `' . $this->getIndexName(PersonRepository::NAME, 1) . '`,
                    ADD UNIQUE `' . $this->getOldIndexName(PersonRepository::NAME, 1) . '`
                        (`user_id`, `unit_id`, `position_id`),
                    DROP INDEX `' . $this->getIndexName(PersonRepository::NAME, 2) . '`,
                    DROP INDEX `' . $this->getIndexName(PersonRepository::NAME, 3) . '`,
                    DROP `order_id`,
                    DROP `group_id`,
                    DROP `action`,
                    DROP `created_at`;
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName(PositionRepository::NAME) . '`
                    DROP INDEX `' . $this->getIndexName(PositionRepository::NAME, 1) . '`,
                    DROP INDEX `' . $this->getIndexName(PositionRepository::NAME, 2) . '`,
                    ADD INDEX `' . $this->getOldIndexName(PositionRepository::NAME, 1) . '` (`type`),
                    ADD INDEX `' . $this->getOldIndexName(PositionRepository::NAME, 2) . '` (`leader`);
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
