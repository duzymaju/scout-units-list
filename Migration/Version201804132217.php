<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\UnitRepository;

/**
 * Version 2018-04-013 22:17
 */
class Version201804132217 extends Version
{
    /**
     * Up
     */
    public function up()
    {
        $this
            ->addSql('
                ALTER TABLE `' . $this->getTableName(UnitRepository::NAME) . '`
                    ADD `marker_url` VARCHAR(100) COLLATE utf8_polish_ci NULL DEFAULT NULL AFTER `location_lng`;
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
                    DROP `marker_url`;
            ')
        ;
    }
}
