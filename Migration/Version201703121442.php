<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;

/**
 * Version 2017-03-12 14:42
 */
class Version201703121442 extends Version
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
                ALTER TABLE `' . $this->getTableName($personName) . '`
                    DROP FOREIGN KEY `' . $this->getOldForeignKeyName($personName, 1) . '`,
                    DROP FOREIGN KEY `' . $this->getOldForeignKeyName($personName, 2) . '`,
                    DROP FOREIGN KEY `' . $this->getOldForeignKeyName($personName, 3) . '`,
                    ADD CONSTRAINT `' . $this->getForeignKeyName($personName, 1) . '`
                        FOREIGN KEY (`unit_id`)
                        REFERENCES `' . $this->getTableName($unitName) . '` (`id`)
                        ON DELETE CASCADE,
                    ADD CONSTRAINT `' . $this->getForeignKeyName($personName, 2) . '`
                        FOREIGN KEY (`position_id`)
                        REFERENCES `' . $this->getTableName($positionName) . '` (`id`)
                        ON DELETE CASCADE,
                    ADD `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0 AFTER `order_no`,
                    ADD `user_name` varchar(250) COLLATE utf8_polish_ci DEFAULT NULL AFTER `sort`,
                    ADD `user_grade` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL AFTER `user_name`;
            ')
            ->addSql('
                UPDATE `' . $this->getTableName($personName) . '`
                    SET `user_name`=(SELECT `display_name` FROM `' . $this->getTableName('users') . '` WHERE `ID`=`user_id`)
                    WHERE `user_name` IS NULL;
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName($unitName) . '`
                    DROP FOREIGN KEY `' . $this->getOldForeignKeyName($unitName, 1) . '`,
                    ADD CONSTRAINT `' . $this->getForeignKeyName($unitName, 1) . '`
                        FOREIGN KEY (`parent_id`)
                        REFERENCES `' . $this->getTableName($unitName) . '` (`id`)
                        ON DELETE CASCADE;
            ')
        ;
    }

    /**
     * Down
     */
    public function down()
    {
        $personName = PersonRepository::NAME;
        $positionName = PositionRepository::NAME;
        $unitName = UnitRepository::NAME;

        $this
            ->addSql('
                ALTER TABLE `' . $this->getTableName($unitName) . '`,
                    DROP FOREIGN KEY `' . $this->getForeignKeyName($unitName, 1) . '`,
                    ADD CONSTRAINT `' . $this->getOldForeignKeyName($unitName, 1) . '`
                        FOREIGN KEY (`parent_id`)
                        REFERENCES `' . $this->getTableName($unitName) . '` (`id`)
                        ON DELETE CASCADE;
            ')
            ->addSql('
                ALTER TABLE `' . $this->getTableName($personName) . '`
                    DROP FOREIGN KEY `' . $this->getForeignKeyName($personName, 1) . '`,
                    DROP FOREIGN KEY `' . $this->getForeignKeyName($personName, 2) . '`,
                    ADD CONSTRAINT `' . $this->getOldForeignKeyName($personName, 1) . '`
                        FOREIGN KEY (`user_id`)
                        REFERENCES `' . $this->getTableName('users') . '` (`ID`)
                        ON DELETE CASCADE,
                    ADD CONSTRAINT `' . $this->getOldForeignKeyName($personName, 2) . '`
                        FOREIGN KEY (`unit_id`)
                        REFERENCES `' . $this->getTableName($unitName) . '` (`id`)
                        ON DELETE CASCADE,
                    ADD CONSTRAINT `' . $this->getOldForeignKeyName($personName, 3) . '`
                        FOREIGN KEY (`position_id`)
                        REFERENCES `' . $this->getTableName($positionName) . '` (`id`)
                        ON DELETE CASCADE,
                    DROP `user_grade`,
                    DROP `user_name`,
                    DROP `sort`;
            ')
        ;
    }

    /**
     * Get old foreign key name
     *
     * @param string $name name
     * @param int    $no   no
     *
     * @return string
     */
    private function getOldForeignKeyName($name, $no)
    {
        $oldForeignKeyName = $this->db->getPrefix() . $this->getForeignKeyName($name, $no);

        return $oldForeignKeyName;
    }
}
