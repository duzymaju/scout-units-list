<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Model\ModelInterface;

/**
 * Native repository
 */
abstract class NativeRepository extends Repository
{
    /**
     * Install
     */
    public function install() {}

    /**
     * Uninstall
     */
    public function uninstall() {}

    /**
     * Get plugin table name
     *
     * @param string|null $name name
     *
     * @return string
     */
    protected function getPluginTableName($name = null)
    {
        if (empty($name)) {
            $name = $this->getName();
        }
        $pluginTableName = $this->getTableName($name);

        return $pluginTableName;
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
