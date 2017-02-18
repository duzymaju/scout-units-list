<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\UnitRepository;

/**
 * Version 2017-02-13 20:46
 */
class Version201702132046 extends Version
{
    /**
     * Up
     */
    public function up()
    {
        $this
            ->addSql('
                ALTER TABLE `' . $this->getTableName(UnitRepository::NAME) . '`
                    DROP INDEX `' . $this->getIndexName(UnitRepository::NAME, 1) . '`,
                    DROP COLUMN `status`,
                    CHANGE `localization_lat` `location_lat` float(10,6) DEFAULT NULL,
                    CHANGE `localization_lng` `location_lng` float(10,6) DEFAULT NULL;
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
                    ADD COLUMN `status` char(1) COLLATE utf8_polish_ci NOT NULL DEFAULT "a" AFTER `id`,
                    ADD INDEX `' . $this->getIndexName(UnitRepository::NAME, 1) . '` (`status`),
                    CHANGE `location_lat` `localization_lat` float(10,6) DEFAULT NULL,
                    CHANGE `location_lng` `localization_lng` float(10,6) DEFAULT NULL;
            ')
        ;
    }
}
