<?php

namespace ScoutUnitsList\Model\Repository;

use DateTime;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\User;

/**
 * User repository
 */
class UserRepository extends NativeRepository
{
    /**
     * Get name
     *
     * @return string
     */
    protected static function getName()
    {
        return 'users';
    }

    /**
     * Get model
     *
     * @return string
     */
    protected static function getModel()
    {
        return User::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this->setStructureElement('id', DbManager::TYPE_DECIMAL, 'ID', true)
            ->setStructureElement('login', DbManager::TYPE_STRING, 'user_login')
            ->setStructureElement('niceName', DbManager::TYPE_STRING, 'user_nicename')
            ->setStructureElement('email', DbManager::TYPE_STRING, 'user_email')
            ->setStructureElement('url', DbManager::TYPE_STRING, 'user_url')
            ->setStructureElement('registered', DbManager::TYPE_STRING, 'user_registered')
            ->setStructureElement('status', DbManager::TYPE_DECIMAL, 'user_status')
            ->setStructureElement('displayName', DbManager::TYPE_STRING, 'display_name');
    }

    /**
     * Find by name
     * 
     * @param string $name name
     *
     * @return array
     */
    public function findByName($name, $limit = 10)
    {
        $query = $this->db->prepare('SELECT ID, user_login, user_nicename FROM wp_users ' .
                'WHERE user_login LIKE :name || user_nicename LIKE :name LIMIT ' . ((int) $limit))
            ->setParam('name', '%' . $this->escapeLike($name) . '%')
            ->getQuery();
        $results = $this->db->getResults($query, ARRAY_A);

        $list = [];
        foreach ($results as $result) {
            $list[] = $this->createObject($result);
        }

        return $list;
    }
}
