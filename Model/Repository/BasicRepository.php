<?php

namespace ScoutUnitsList\Model\Repository;

use ReflectionClass;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Manager\DbStatementManager;

/**
 * Basic repository
 */
abstract class BasicRepository
{
    /** @var DbManager */
    protected $db;

    /**
     * Constructor
     *
     * @param DbManager $dbManager database manager
     */
    public function __construct(DbManager $dbManager)
    {
        $this->db = $dbManager;
    }

    /**
     * Get name
     *
     * @return string
     */
    abstract protected static function getName();

    /**
     * Get model
     *
     * @return string
     */
    abstract protected static function getModel();

    /**
     * Get map
     *
     * @return array
     */
    abstract protected static function getMap();

    /**
     * Install
     */
    abstract public function install();

    /**
     * Uninstall
     */
    abstract public function uninstall();

    /**
     * Get table name
     *
     * @param string $name name
     *
     * @return string
     */
    protected function getTableName($name)
    {
        $tableName = $this->db->getPerfix() . $name;

        return $tableName;
    }

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
        $pluginTableName = $this->getTableName('sul_' . $name);

        return $pluginTableName;
    }

    /**
     * Get index name
     *
     * @param int $no no
     * 
     * @return string
     */
    protected function getIndexName($no)
    {
        $indexName = static::getName() . '_index_' . $no;

        return $indexName;
    }

    /**
     * Create object
     *
     * @param array $tableData table data
     *
     * @return object
     */
    private function createObject(array $tableData)
    {
        $modelClass = static::getModel();
        $model = new $modelClass();
        $modelReflector = new ReflectionClass($modelClass);
        foreach (static::getMap() as $tableKey => $objectKey) {
            if (array_key_exists($tableKey, $tableData)) {
                $setterMethod = 'set' . ucfirst($objectKey);
                $value = $tableData[$tableKey];
                if (method_exists($model, $setterMethod)) {
                    $model->$setterMethod($value);
                } else {
                    if (is_numeric($value)) {
                        $value = (int) $value;
                    }
                    $property = $modelReflector->getProperty($objectKey);
                    $property->setAccessible(true);
                    $property->setValue($model, $value);
                }
            }
        }

        return $model;
    }

    public function save($object)
    {
        // @TODO
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
        $query = 'SELECT * FROM ' . $this->getPluginTableName();

        $where = [];
        foreach ($conditions as $key => $value) {
            $key = $this->escape($key);
            $where[] = is_array($value) ? $key . ' IN (:' . $key . ')' : $key . '=:' . $key;
        }
        if (count($where) > 0) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $queryOrder = [];
        foreach ($order as $key => $direction) {
            $direction = strtoupper($direction);
            if ($direction == 'ASC' || $direction == 'DESC') {
                $queryOrder[] = $this->escape($key) . ' ' . $direction;
            }
        }
        if (count($queryOrder) > 0) {
            $query .= ' ORDER BY ' . implode(', ', $queryOrder);
        }

        if (is_int($limit) && $limit >= 0 && is_int($offset) && $offset >= 0) {
            $query .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        $statement = $this->db->prepare($query);
        foreach ($conditions as $key => $value) {
            $type = is_int($value) ? DbStatementManager::TYPE_DECIMAL :
                (is_float($value) ? DbStatementManager::TYPE_FLOAT : DbStatementManager::TYPE_STRING);
            $statement->setParam($key, $value, $type);
        }
        $results = $this->db->getResults($statement->getQuery(), ARRAY_A);

        $items = [];
        foreach ($results as $result) {
            $items[] = $this->createObject($result);
        }

        return $items;
    }

    /**
     * Get one by
     *
     * @param array $conditions conditions
     * @param array $order      order
     *
     * @return object|null
     */
    public function getOneBy(array $conditions, array $order = [])
    {
        $items = $this->getBy($conditions, $order, 1);
        $item = array_shift($items);

        return $item;
    }

    /**
     * Escape
     *
     * @param string $text text
     *
     * @return string
     */
    protected function escape($text)
    {
        return mysqli_real_escape_string($text);
    }
}
