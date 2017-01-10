<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\ModelInterface;
use ScoutUnitsList\Model\User;
use WP_User;

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
    protected function getName()
    {
        return 'users';
    }

    /**
     * Get model
     *
     * @return string
     */
    protected function getModel()
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
     * Create model
     *
     * @param array $tableData table data
     *
     * @return User
     */
    protected function createModel(array $tableData)
    {
        $user = parent::createModel($tableData);
        $this->completeUserModel($user);

        return $user;
    }

    /**
     * Create model from WP user
     *
     * @param WP_User $wpUser WP user
     *
     * @return User
     */
    public function createModelFromWpUser(WP_User $wpUser)
    {
        $modelClass = $this->getModel();
        $user = new $modelClass();
        foreach ($this->getMap() as $modelKey => $objectKey) {
            if (isset($wpUser->$objectKey)) {
                $this->setValue($user, $modelKey, $wpUser->$objectKey);
            }
        }
        $this->completeUserModel($user);

        return $user;
    }

    /**
     * Save
     *
     * @param ModelInterface $model model
     *
     * @return self
     */
    public function save(ModelInterface $model)
    {
        /** @var User $user */
        $user = $model;
        $this->setProperPublishEmail($user);
        update_user_meta($user->getId(), 'sul_publish_email', $user->getPublishEmail());
        update_user_meta($user->getId(), 'sul_grade', $user->getGrade());
        update_user_meta($user->getId(), 'sul_duty', $user->getDuty());
        update_user_meta($user->getId(), 'sul_sex', $user->getSex());

        return $this;
    }

    /**
     * Complete user model
     *
     * @param User $user user
     *
     * @return User
     */
    protected function completeUserModel(User $user)
    {
        // @README: This method could cause high database load when used for users list
        $user->setPublishEmail((int) get_the_author_meta('sul_publish_email', $user->getId()))
            ->setGrade(get_the_author_meta('sul_grade', $user->getId()))
            ->setDuty(get_the_author_meta('sul_duty', $user->getId()))
            ->setSex(get_the_author_meta('sul_sex', $user->getId()));
        $this->setProperPublishEmail($user);

        return $user;
    }

    /**
     * Get by
     *
     * @param array    $conditions conditions
     * @param array    $order      order
     * @param int|null $limit      limit
     * @param int      $offset     offset
     *
     * @return array
     */
    public function getBy(array $conditions, array $order = [], $limit = null, $offset = 0)
    {
        $users = parent::getBy($conditions, $order, $limit, $offset);
        foreach ($users as $user) {
            $this->completeUserModel($user);
        }

        return $users;
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
        $query = $this->db->prepare('SELECT ID, user_login, display_name FROM wp_users ' .
                'WHERE user_login LIKE :name || user_nicename LIKE :name LIMIT ' . ((int) $limit))
            ->setParam('name', '%' . $this->escapeLike($name) . '%')
            ->getQuery();
        $results = $this->db->getResults($query, ARRAY_A);

        $list = [];
        foreach ($results as $result) {
            $list[] = $this->createModel($result);
        }

        return $list;
    }

    /**
     * Get current user
     *
     * @return User|null
     */
    public function getCurrentUser()
    {
        $wpUser = wp_get_current_user();
        if ($wpUser->ID == 0) {
            return null;
        }
        $user = $this->createModelFromWpUser($wpUser);

        return $user;
    }

    /**
     * Get publish e-mails
     *
     * @return array
     */
    public function getPublishEmails()
    {
        return [
            User::PUBLISH_EMAIL_FORM => __('Contact form', 'scout-units-list'),
            User::PUBLISH_EMAIL_YES => __('Yes', 'scout-units-list'),
            User::PUBLISH_EMAIL_NO => __('No', 'scout-units-list'),
        ];
    }

    /**
     * Set proper publish e-mail
     *
     * @param User $user user
     * 
     * @return self
     */
    protected function setProperPublishEmail(User $user)
    {
        if (!array_key_exists($user->getPublishEmail(), $this->getPublishEmails())) {
            $user->setPublishEmail(User::PUBLISH_EMAIL_FORM);
        }

        return $this;
    }
}
