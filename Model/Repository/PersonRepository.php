<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Person;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\Model\User;

/**
 * Person repository
 */
class PersonRepository extends Repository
{
    /** @const string */
    const NAME = 'sul_persons';

    /**
     * Get model
     *
     * @return string
     */
    protected function getModel()
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
     * Is unit leader
     *
     * @param User $user user
     * @param Unit $unit unit
     *
     * @return bool
     */
    public function isUnitLeader(User $user, Unit $unit)
    {
        $ids = $this->getSubordinateUnitIds($user);
        $isLeader = in_array($unit->getId(), $ids);

        return $isLeader;
    }

    /**
     * Get subordinate unit IDs
     *
     * @param User $user user
     *
     * @return array
     */
    public function getSubordinateUnitIds(User $user)
    {
        $query = $this->db
            ->prepare('
                SELECT pe.unit_id
                    FROM `' . $this->getTableName() . '` pe
                    INNER JOIN `' . $this->getTableName(PositionRepository::NAME) . '` po
                    ON pe.position_id = po.id
                    WHERE pe.user_id = :userId && po.leader = :leader
            ')
            ->setParam('userId', $user->getId())
            ->setParam('leader', 1)
            ->getQuery();
        $ids = [];
        foreach ($this->db->getColumn($query) as $id) {
            $ids[] = (int) $id;
        }

        return $ids;
    }

    /**
     * Set persons to units
     *
     * @param Unit[]             $units              units
     * @param UserRepository     $userRepository     user repository
     * @param PositionRepository $positionRepository position repository
     * @param bool               $includeUsers       include users
     * @param bool               $leaderOnly         leader only
     *
     * @return self
     */
    public function setPersonsToUnits(array $units, UserRepository $userRepository,
        PositionRepository $positionRepository, $includeUsers = false, $leaderOnly = true)
    {
        // Get persons
        $unitsByIds = [];
        foreach ($units as $unit) {
            $unitsByIds[$unit->getId()] = $unit;
        }
        $persons = $this->getBy([
            'unitId' => array_keys($unitsByIds),
        ]);

        // Get positions
        $positionIds = [];
        foreach ($persons as $person) {
            $positionIds[] = $person->getPositionId();
        }
        $positionsParams = [
            'id' => array_unique($positionIds),
        ];
        if ($leaderOnly) {
            $positionsParams['leader'] = 1;
        }
        $positions = $positionRepository->getBy($positionsParams);
        $positionsByIds = [];
        foreach ($positions as $position) {
            $positionsByIds[$position->getId()] = $position;
        }

        // Set positions to persons
        $userIds = [];
        foreach ($persons as $person) {
            if (array_key_exists($person->getPositionId(), $positionsByIds)) {
                $person->setPosition($positionsByIds[$person->getPositionId()]);
                $userIds[] = $person->getUserId();
            }
        }

        // Include users if necessary
        $usersByIds = [];
        if ($includeUsers) {
            $users = $userRepository->getBy([
                'id' => $userIds,
            ]);
            foreach ($users as $user) {
                $usersByIds[$user->getId()] = $user;
            }
        }

        // Add complete persons to units
        foreach ($persons as $person) {
            if ((!$includeUsers || array_key_exists($person->getUserId(), $usersByIds)) &&
                array_key_exists($person->getUnitId(), $unitsByIds) && $person->getPosition()) {
                if ($includeUsers) {
                    $person->setUnit($unitsByIds[$person->getUnitId()]);
                }
                $person->setUser($usersByIds[$person->getUserId()]);
                $unitsByIds[$person->getUnitId()]->addPerson($person);
            }
        }

        return $this;
    }
}
