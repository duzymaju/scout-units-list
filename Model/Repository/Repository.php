<?php

namespace ScoutUnitsList\Model\Repository;

use ReflectionClass;
use ScoutUnitsList\Exception\NotFoundException;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Manager\DbStatementManager;
use ScoutUnitsList\Model\ModelInterface;
use ScoutUnitsList\System\Tools\Paginator;

/**
 * Repository
 */
abstract class Repository
{
    /** @var DbManager */
    protected $db;

    /** @var array */
    protected $structure = [];

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
        $this->structure[$key] = [
            'dbKey' => empty($dbKey) ? $key : $dbKey,
            'isId' => $isId,
            'type' => $type,
        ];

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
        $map = [];
        foreach ($this->structure as $key => $element) {
            if ($idIncluded || !$element['isId']) {
                $map[$key] = $fullInfo ? $element : $element['dbKey'];
            }
        }

        return $map;
    }

    /**
     * Create model
     *
     * @param array $tableData table data
     *
     * @return object
     */
    protected function createModel(array $tableData)
    {
        $modelClass = static::getModel();
        $model = new $modelClass();
        foreach ($this->getMap() as $modelKey => $tableKey) {
            if (array_key_exists($tableKey, $tableData)) {
                $this->setValue($model, $modelKey, $tableData[$tableKey]);
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
            $ucName = ucfirst($key);
            $methodGet = 'get' . $ucName;
            $methodIs = 'is' . $ucName;
            $methodHas = 'has' . $ucName;
            if (method_exists($model, $methodGet)) {
                $statement->setParam($element['dbKey'], $model->$methodGet(), $element['type']);
            } elseif (method_exists($model, $methodIs)) {
                $statement->setParam($element['dbKey'], $model->$methodIs(), $element['type']);
            } elseif (method_exists($model, $methodHas)) {
                $statement->setParam($element['dbKey'], $model->$methodHas(), $element['type']);
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
     * Count by
     *
     * @param array $conditions conditions
     *
     * @return array
     */
    public function countBy(array $conditions)
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->getPluginTableName();

        $where = $this->getConditionsToQuery($conditions);
        if (count($where) > 0) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $statement = $this->setConditionsToStatement($this->db->prepare($query), $conditions);
        $count = $this->db->getVariable($statement->getQuery());

        return $count;
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

        $where = $this->getConditionsToQuery($conditions);
        if (count($where) > 0) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $queryOrder = [];
        foreach ($order as $key => $direction) {
            $dbKey = $this->getDbKey($key);
            $direction = strtoupper($direction);
            if (isset($dbKey) && ($direction == 'ASC' || $direction == 'DESC')) {
                $queryOrder[] = $this->escape($dbKey) . ' ' . $direction;
            }
        }
        if (count($queryOrder) > 0) {
            $query .= ' ORDER BY ' . implode(', ', $queryOrder);
        }

        if (is_int($limit) && $limit >= 0 && is_int($offset) && $offset >= 0) {
            $query .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        $statement = $this->setConditionsToStatement($this->db->prepare($query), $conditions);
        $results = $this->db->getResults($statement->getQuery(), ARRAY_A);

        $items = [];
        foreach ($results as $result) {
            $items[] = $this->createModel($result);
        }

        return $items;
    }

    /**
     * Get paginator by
     *
     * @param array $conditions conditions
     * @param array $order      order
     * @param int   $packSize   pack size
     * @param int   $packNo     pack no
     *
     * @return array
     */
    public function getPaginatorBy(array $conditions, array $order = [], $packSize = 20, $packNo = 1)
    {
        $limit = $packSize;
        $offset = $packSize * ($packNo - 1);
        $items = $this->getBy($conditions, $order, $limit, $offset);

        $paginator = new Paginator($items, $packNo, $packSize, $order);
        $paginator->totalSize = $this->countBy($conditions);

        return $paginator;
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
     * Get conditions to query
     *
     * @param array $conditions conditions
     *
     * @return array
     */
    private function getConditionsToQuery(array $conditions)
    {
        $where = [];
        foreach ($conditions as $key => $value) {
            $dbKey = $this->getDbKey($key);
            if (isset($dbKey)) {
                $key = $this->escape($dbKey);
                $where[] = is_array($value) ? $key . ' IN (:' . $key . ')' : $key . '=:' . $key;
            }
        }

        return $where;
    }

    /**
     * Set conditions to statement
     *
     * @param DbStatementManager $statement  statement
     * @param array              $conditions conditions
     *
     * @return DbStatementManager
     */
    private function setConditionsToStatement(DbStatementManager $statement, array $conditions)
    {
        foreach ($conditions as $key => $value) {
            $dbKey = $this->getDbKey($key);
            if (isset($dbKey)) {
                $type = is_int($value) ? DbManager::TYPE_DECIMAL :
                    (is_float($value) ? DbManager::TYPE_FLOAT : DbManager::TYPE_STRING);
                $statement->setParam($dbKey, $value, $type);
            }
        }

        return $statement;
    }

    /**
     * Get DB key
     *
     * @param string $key key
     *
     * @return string|null
     */
    private function getDbKey($key)
    {
        $dbKey = array_key_exists($key, $this->structure) ? $this->structure[$key]['dbKey'] : null;

        return $dbKey;
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
        return $this->db->escape($text);
    }

    /**
     * Escape for LIKE statements
     * Has to be use before escape method
     *
     * @param string $text text
     *
     * @return string
     */
    protected function escapeLike($text)
    {
        return $this->db->escapeLike($text);
    }
}