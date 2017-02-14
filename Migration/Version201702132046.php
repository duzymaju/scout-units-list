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
                    DROP COLUMN `status`;
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
            ')
        ;
    }
}
