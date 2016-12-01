<?php

namespace ScoutUnitsList\Model\Repository;

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
            $name = static::getName();
        }
        $pluginTableName = $this->getTableName($name);

        return $pluginTableName;
    }

    /**
     * Save
     *
     * @return self
     */
    public function save()
    {
        return $this;
    }

    /**
     * Delete
     *
     * @return self
     */
    public function delete()
    {
        return $this;
    }
}
