<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Position;

/**
 * Position repository
 */
class PositionRepository extends Repository
{
    /** @const string */
    const NAME = 'sul_positions';

    /**
     * Get model
     *
     * @return string
     */
    protected function getModel()
    {
        return Position::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this->setStructureElement('id', DbManager::TYPE_DECIMAL, null, true)
            ->setStructureElement('type', DbManager::TYPE_STRING)
            ->setStructureElement('nameMale', DbManager::TYPE_STRING, 'name_male')
            ->setStructureElement('nameFemale', DbManager::TYPE_STRING, 'name_female')
            ->setStructureElement('description', DbManager::TYPE_STRING)
            ->setStructureElement('leader', DbManager::TYPE_DECIMAL);
    }
}
