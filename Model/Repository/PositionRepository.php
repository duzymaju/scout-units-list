<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Position;

/**
 * Position repository
 */
class PositionRepository extends BasicRepository
{
    /**
     * Get name
     *
     * @return string
     */
    protected static function getName()
    {
        return 'positions';
    }

    /**
     * Get model
     *
     * @return string
     */
    protected static function getModel()
    {
        return Position::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this->setStructureElement('id', DbManager::TYPE_DECIMAL, null, true)
            ->setStructureElement('nameMale', DbManager::TYPE_STRING, 'name_male')
            ->setStructureElement('nameFemale', DbManager::TYPE_STRING, 'name_female')
            ->setStructureElement('leader', DbManager::TYPE_DECIMAL);
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
                `name_male` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                `name_female` varchar(50) COLLATE utf8_polish_ci NOT NULL,
                `leader` bool NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                INDEX `' . $this->getIndexName(1) . '` (`leader`)
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
