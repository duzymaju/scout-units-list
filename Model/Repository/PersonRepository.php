<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Person;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;

/**
 * Person repository
 */
class PersonRepository extends BasicRepository
{
    /**
     * Get name
     *
     * @return string
     */
    protected static function getName()
    {
        return 'persons';
    }

    /**
     * Get model
     *
     * @return string
     */
    protected static function getModel()
    {
        return Person::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this->setStructureElement('id', DbManager::TYPE_DECIMAL, null, true)
            ->setStructureElement('userId', DbManager::TYPE_DECIMAL, 'user_id')
            ->setStructureElement('unitId', DbManager::TYPE_DECIMAL, 'unit_id')
            ->setStructureElement('positionId', DbManager::TYPE_DECIMAL, 'position_id')
            ->setStructureElement('orderNo', DbManager::TYPE_STRING, 'order_no');
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
                `user_id` bigint(20) UNSIGNED NOT NULL,
                `unit_id` int(10) UNSIGNED NOT NULL,
                `position_id` int(10) UNSIGNED NOT NULL,
                `order_no` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE `' . $this->getIndexName(1) . '` (`user_id`, `unit_id`, `position_id`),
                FOREIGN KEY (user_id)
                    REFERENCES `' . $this->getTableName('users') . '` (`ID`)
                    ON DELETE CASCADE,
                FOREIGN KEY (unit_id)
                    REFERENCES `' . $this->getPluginTableName(UnitRepository::getName()) . '` (`id`)
                    ON DELETE CASCADE,
                FOREIGN KEY (position_id)
                    REFERENCES `' . $this->getPluginTableName(PositionRepository::getName()) . '` (`id`)
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
