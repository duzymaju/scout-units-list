<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;

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
                ALTER TABLE `' . $this->getTableName(PersonRepository::NAME) . '`
                    ADD `order_id` INT UNSIGNED NULL AFTER `position_id`;
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName(UnitRepository::NAME) . '`
                    ADD `order_id` INT UNSIGNED NULL AFTER `parent_id`;
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
                    DROP `order_id`;
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName(PersonRepository::NAME) . '`
                    DROP `order_id`;
            ')
        ;
    }
}
