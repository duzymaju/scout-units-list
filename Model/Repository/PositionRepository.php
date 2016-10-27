<?php

namespace ScoutUnitsList\Model\Repository;

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
     * Get map
     *
     * @return array
     */
    protected static function getMap()
    {
        $map = [
            'id' => 'id',
            'name_male' => 'nameMale',
            'name_female' => 'nameFemale',
            'leader' => 'leader',
        ];

        return $map;
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
