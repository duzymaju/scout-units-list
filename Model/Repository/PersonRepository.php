<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Exception\InvalidArgumentException;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Person;
use ScoutUnitsList\Model\Position;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\Model\User;

/**
 * Person repository
 */
class PersonRepository extends VersionedRepository
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
        $this
            ->setStructureElement('id', DbManager::TYPE_DECIMAL, null, true)
            ->setStructureElement('userId', DbManager::TYPE_DECIMAL, 'user_id')
            ->setStructureElement('unitId', DbManager::TYPE_DECIMAL, 'unit_id')
            ->setStructureElement('positionId', DbManager::TYPE_DECIMAL, 'position_id')
            ->setStructureElement('orderId', DbManager::TYPE_DECIMAL, 'order_id')
            ->setStructureElement('orderNo', DbManager::TYPE_STRING, 'order_no')
            ->setStructureElement('sort', DbManager::TYPE_DECIMAL)
            ->setStructureElement('userName', DbManager::TYPE_STRING, 'user_name')
            ->setStructureElement('userGrade', DbManager::TYPE_STRING, 'user_grade')
        ;
        parent::defineStructure();
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
                SELECT pe.`unit_id`
                    FROM `' . $this->getTableName() . '` pe
                    INNER JOIN `' . $this->getTableName(PositionRepository::NAME) . '` po
                    ON pe.`position_id` = po.`id`
                    WHERE pe.`user_id` = :userId && po.`leader` = :leader && pe.`group_id` = 0 &&
                        pe.`action` IN (:actions)
            ')
            ->setParam('userId', $user->getId())
            ->setParam('leader', 1)
            ->setParam('actions', $this->getActionsExceptRemoved())
            ->getQuery();
        $ids = [];
        foreach ($this->db->getColumn($query) as $id) {
            $ids[] = (int) $id;
        }

        return $ids;
    }

    /**
     * Is position in use
     *
     * @param Position $position position
     *
     * @return bool
     */
    public function isPositionInUse(Position $position)
    {
        $query = $this->db
            ->prepare('
                SELECT COUNT(`id`)
                    FROM `' . $this->getTableName() . '`
                    WHERE `position_id` = :positionId
            ')
            ->setParam('positionId', $position->getId())
            ->getQuery();
        $personsNumber = (int) $this->db->getVariable($query);

        return $personsNumber > 0;
    }

    /**
     * Set persons to units
     *
     * @param Unit[]                    $units                units
     * @param PositionRepository        $positionRepository   position repository
     * @param UserRepository|null       $userRepository       user repository (to include users)
     * @param AttachmentRepository|null $attachmentRepository attachment repository (to include orders)
     * @param bool                      $leaderOnly           leader only
     *
     * @return self
     */
    public function setPersonsToUnits(array $units, PositionRepository $positionRepository,
        UserRepository $userRepository = null, AttachmentRepository $attachmentRepository = null, $leaderOnly = false)
    {
        // Get persons
        $unitsByIds = [];
        foreach ($units as $unit) {
            $unitsByIds[$unit->getId()] = $unit;
        }
        $persons = $this->getBy([
            'unitId' => array_keys($unitsByIds),
        ], [
            'sort' => 'asc',
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
        $positions = count($positionsParams['id']) > 0 ? $positionRepository->getBy($positionsParams) : [];
        $positionsByIds = [];
        foreach ($positions as $position) {
            $positionsByIds[$position->getId()] = $position;
        }

        // Set positions to persons
        $userIds = [];
        $orderIds = [];
        foreach ($persons as $person) {
            if (array_key_exists($person->getPositionId(), $positionsByIds)) {
                $person->setPosition($positionsByIds[$person->getPositionId()]);
                $userIds[] = $person->getUserId();
                if ($person->getOrderId()) {
                    $orderIds[] = $person->getOrderId();
                }
            }
        }

        // Include users if necessary
        $usersByIds = [];
        if ($userRepository && count($userIds) > 0) {
            $users = $userRepository->getBy([
                'id' => $userIds,
            ]);
            foreach ($users as $user) {
                $usersByIds[$user->getId()] = $user;
            }
        }

        // Include orders if necessary
        $ordersByIds = [];
        if ($attachmentRepository && count($orderIds) > 0) {
            $orders = $attachmentRepository->getBy([
                'id' => $orderIds,
            ]);
            foreach ($orders as $order) {
                $ordersByIds[$order->getId()] = $order;
            }
        }

        // Add complete persons to units
        foreach ($persons as $person) {
            if (array_key_exists($person->getUnitId(), $unitsByIds) && $person->getPosition()) {
                if ($userRepository && array_key_exists($person->getUserId(), $usersByIds)) {
                    $person->setUser($usersByIds[$person->getUserId()]);
                }
                $person->setUnit($unitsByIds[$person->getUnitId()]);
                $unitsByIds[$person->getUnitId()]->addPerson($person);
            }
            if ($attachmentRepository && array_key_exists($person->getOrderId(), $ordersByIds)) {
                $person->setOrder($ordersByIds[$person->getOrderId()]);
            }
        }

        return $this;
    }

    /**
     * Sort persons for unit
     *
     * @param Unit               $unit               unit
     * @param array              $order              order
     * @param PositionRepository $positionRepository position repository
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function sortPersonsForUnit(Unit $unit, array $order, PositionRepository $positionRepository)
    {
        $this->setPersonsToUnits([
            $unit,
        ], $positionRepository);

        $orderedPersons = [];
        foreach ($unit->getPersons() as $person) {
            if (false !== $index = array_search($person->getId(), $order)) {
                $orderedPersons[$index] = $person;
            }
        }
        ksort($orderedPersons);

        if (array_keys($order) !== array_keys($orderedPersons)) {
            throw new InvalidArgumentException('Person IDs in order list for selected unit are incorrect.');
        }

        foreach ($orderedPersons as $i => $person) {
            $person->setSort($i + 1);
            $this->save($person, false);
        }

        return $this;
    }
}
