<?php

namespace ScoutUnitsList\Migration;

use ScoutUnitsList\Manager\DbManager;

/**
 * Version
 */
abstract class Version
{
    /** @var DbManager */
    protected $db;

    /**
     * Construct
     *
     * @param DbManager $db database manager
     */
    public function __construct(DbManager $db)
    {
        $this->db = $db;
    }

    /**
     * Up
     */
    abstract public function up();

    /**
     * Down
     */
    abstract public function down();

    /**
     * Get table name
     *
     * @param string $name name
     *
     * @return string
     */
    protected function getTableName($name)
    {
        $tableName = $this->db->getPrefix() . $name;

        return $tableName;
    }

    /**
     * Get index name
     *
     * @param string $name name
     * @param int    $no   no
     *
     * @return string
     */
    protected function getIndexName($name, $no)
    {
        $indexName = $name . '_index_' . $no;

        return $indexName;
    }

    /**
     * Get foreign key name
     *
     * @param string $name name
     * @param int    $no   no
     *
     * @return string
     */
    protected function getForeignKeyName($name, $no)
    {
        $foreignKeyName = $name . '_ibfk_' . $no;

        return $foreignKeyName;
    }

    /**
     * Add SQL
     *
     * @param string $sql SQL
     *
     * @return self
     */
    protected function addSql($sql)
    {
        $this->db->query($sql);

        return $this;
    }
}
