<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\PositionRepository;

/**
 * Version 2017-07-08 16:05
 */
class Version201707081605 extends Version
{
    /**
     * Up
     */
    public function up()
    {
        $this
            ->addSql('
                ALTER TABLE `' . $this->getTableName(PositionRepository::NAME) . '`
                    ADD `responsibilities` text COLLATE utf8_polish_ci DEFAULT NULL AFTER `description`;
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
                ALTER TABLE `' . $this->getTableName(PositionRepository::NAME) . '`
                    DROP `responsibilities`;
            ')
        ;
    }
}
