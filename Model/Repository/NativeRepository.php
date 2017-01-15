<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Model\ModelInterface;

/**
 * Native repository
 */
abstract class NativeRepository extends Repository
{
    /**
     * Save
     *
     * @param ModelInterface $model model
     *
     * @return self
     */
    public function save(ModelInterface $model)
    {
        unset($model);

        return $this;
    }

    /**
     * Delete
     *
     * @param ModelInterface $model model
     *
     * @return self
     */
    public function delete(ModelInterface $model)
    {
        unset($model);

        return $this;
    }
}
