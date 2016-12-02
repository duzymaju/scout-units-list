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
                    FROM `' . $this->getPluginTableName() . '` pe
                    INNER JOIN `' . $this->getPluginTableName(PositionRepository::getName()) . '` po
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
     * Get persons for unit
     *
     * @param int                $unitId             unit ID
     * @param UserRepository     $userRepository     user repository
     * @param PositionRepository $positionRepository position repository
     *
     * @return array
     */
    public function getPersonsForUnit($unitId, UserRepository $userRepository, PositionRepository $positionRepository)
    {
        $persons = $this->getBy([
            'unitId' => $unitId,
        ]);

        $userIds = [];
        $positionIds = [];
        $personsByUserIds = [];
        $personsByPositionIds = [];
        foreach ($persons as $person) {
            $userId = $person->getUserId();
            $userIds[] = $userId;

            $positionId = $person->getPositionId();
            $positionIds[] = $positionId;

            if (!array_key_exists($userId, $personsByUserIds)) {
                $personsByUserIds[$userId] = [];
            }
            $personsByUserIds[$userId][] = $person;

            if (!array_key_exists($positionId, $personsByPositionIds)) {
                $personsByPositionIds[$positionId] = [];
            }
            $personsByPositionIds[$positionId][] = $person;
        }

        $users = $userRepository->getBy([
            'id' => array_unique($userIds),
        ]);
        foreach ($users as $user) {
            $userId = $user->getId();
            foreach ($personsByUserIds[$userId] as $person) {
                $person->setUser($user);
            }
        }

        $positions = $positionRepository->getBy([
            'id' => array_unique($positionIds),
        ]);
        foreach ($positions as $position) {
            $positionId = $position->getId();
            foreach ($personsByPositionIds[$positionId] as $person) {
                $person->setPosition($position);
            }
        }

        return $persons;
    }
}
