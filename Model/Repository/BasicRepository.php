<?php

namespace ScoutUnitsList\Model\Repository;

use ReflectionClass;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\ModelInterface;

/**
 * Basic repository
 */
abstract class BasicRepository
{
    /** @var DbManager */
    protected $db;

    /** @var array */
    protected $structure = array();

    /**
     * Constructor
     *
     * @param DbManager $dbManager database manager
     */
    public function __construct(DbManager $dbManager)
    {
        $this->db = $dbManager;
        $this->defineStructure();
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
     * Define structure
     */
    abstract protected function defineStructure();

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
     * Set structure element
     *
     * @param string      $key   key
     * @param string      $type  type
     * @param string|null $dbKey database key
     * @param bool        $isId  is ID
     *
     * @return self
     */
    protected function setStructureElement($key, $type, $dbKey = null, $isId = false)
    {
        $this->structure[$key] = array(
            'dbKey' => empty($dbKey) ? $key : $dbKey,
            'isId' => $isId,
            'type' => $type,
        );

        return $this;
    }

    /**
     * Get map
     *
     * @param bool $idIncluded ID included
     * @param bool $fullInfo   full info
     *
     * @return array
     */
    protected function getMap($idIncluded = true, $fullInfo = false)
    {
        $map = array();
        foreach ($this->structure as $key => $element) {
            if ($idIncluded || !$element['isId']) {
                $map[$key] = $fullInfo ? $element : $element['dbKey'];
            }
        }

        return $map;
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
        foreach ($this->getMap() as $objectKey => $tableKey) {
            if (array_key_exists($tableKey, $tableData)) {
                $this->setValue($model, $objectKey, $tableData[$tableKey]);
            }
        }

        return $model;
    }

    /**
     * Set value
     *
     * @param ModelInterface $model model
     * @param string         $key   key
     * @param mixed          $value value
     *
     * @return self
     */
    protected function setValue(ModelInterface $model, $key, $value)
    {
        $setterMethod = 'set' . ucfirst($key);
        if (method_exists($model, $setterMethod)) {
            $model->$setterMethod($value);
        } else {
            if (is_numeric($value)) {
                $value = (int) $value;
            }
            $modelReflector = new ReflectionClass(static::getModel());
            $property = $modelReflector->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($model, $value);
        }

        return $this;
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
        if ($model->getId() == null) {
            $id = $this->db->getAutoIncrement($this->getPluginTableName());
            $this->setValue($model, 'id', $id);
            $statement = $this->db->insert($this->getPluginTableName());
            $statement->setParam('id', $id, DbManager::TYPE_DECIMAL);
        } else {
            $statement = $this->db->update($this->getPluginTableName());
            $statement->setCondition('id', $model->getId(), DbManager::TYPE_DECIMAL);
        }

        foreach ($this->getMap(false, true) as $key => $element) {
            $methodName = 'get' . ucfirst($key);
            if (method_exists($model, $methodName)) {
                $statement->setParam($element['dbKey'], $model->$methodName(), $element['type']);
            }
        }
        $statement->execute();

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
        if ($model->getId() == null) {
            return $this;
        }
        $this->db->delete($this->getPluginTableName())
            ->setCondition('id', $model->getId(), DbManager::TYPE_DECIMAL)
            ->execute();

        return $this;
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
            $type = is_int($value) ? DbManager::TYPE_DECIMAL :
                (is_float($value) ? DbManager::TYPE_FLOAT : DbManager::TYPE_STRING);
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
     * Get one by or 404
     *
     * @param array $conditions conditions
     * @param array $order      order
     *
     * @return object
     *
     * @throws NotFoundException
     */
    public function getOneByOr404(array $conditions, array $order = [])
    {
        $item = $this->getOneBy($conditions, $order);
        if (!isset($item)) {
            throw new NotFoundException();
        }

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
        return esc_sql($text);
    }
}
