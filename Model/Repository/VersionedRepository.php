<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Exception\RepositoryException;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\VersionedModelInterface;
use ScoutUnitsList\System\Tools\DateTime;

/**
 * Versioned repository
 */
abstract class VersionedRepository extends Repository
{
    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this
            ->setStructureElement('groupId', DbManager::TYPE_DECIMAL, 'group_id')
            ->setStructureElement('action', DbManager::TYPE_STRING)
            ->setStructureElement('createdAt', DbManager::TYPE_DATETIME, 'created_at')
        ;
    }

    /**
     * Save
     *
     * @param VersionedModelInterface $model model
     *
     * @return self
     *
     * @throws RepositoryException
     */
    public function save(VersionedModelInterface $model)
    {
        if ($model->getId() == null) {
            $model
                ->setGroupId(0)
                ->setAction(VersionedModelInterface::ACTION_INSERTED)
                ->setCreatedAt(new DateTime())
            ;
        } else {
            $oldModel = $this->getOneBy([
                'id' => $model->getId(),
            ]);
            if (!isset($oldModel)) {
                throw new RepositoryException(sprintf('Model %s (ID %d) doesn\'t exist in database.',
                    get_class($model), $model->getId()));
            }

            $oldVersion = $this->copy($oldModel);
            $oldVersion->setGroupId($model->getId());
            $this->modifyOldVersion($oldVersion);
            parent::save($oldVersion);

            $model
                ->setGroupId(0)
                ->setAction(VersionedModelInterface::ACTION_MODIFIED)
                ->setCreatedAt(new DateTime())
            ;
        }
        parent::save($model);

        return $this;
    }

    /**
     * Delete
     *
     * @param VersionedModelInterface $model model
     *
     * @return self
     */
    public function delete(VersionedModelInterface $model)
    {
        $model->setAction(VersionedModelInterface::ACTION_REMOVED);
        parent::save($model);

        return $this;
    }

    /**
     * Count by
     *
     * @param array $conditions conditions
     *
     * @return array
     */
    public function countBy(array $conditions)
    {
        $response = parent::countBy($this->improveConditions($conditions));

        return $response;
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
        $response = parent::getBy($this->improveConditions($conditions), $order, $limit, $offset);

        return $response;
    }

    /**
     * Improve conditions
     *
     * @param array $conditions conditions
     *
     * @return array
     */
    private function improveConditions(array $conditions)
    {
        $conditions['groupId'] = 0;
        $conditions['action'] = $this->getActionsExceptRemoved();

        return $conditions;
    }

    /**
     * Get actions except removed
     *
     * @return array
     */
    protected function getActionsExceptRemoved()
    {
        $actions = [
            VersionedModelInterface::ACTION_INSERTED,
            VersionedModelInterface::ACTION_MODIFIED,
        ];

        return $actions;
    }

    /**
     * Modify old version
     *
     * @param VersionedModelInterface $oldVersion old version
     *
     * @return VersionedModelInterface
     */
    protected function modifyOldVersion(VersionedModelInterface $oldVersion)
    {
        return $oldVersion;
    }
}
