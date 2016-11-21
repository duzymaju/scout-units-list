<?php

namespace ScoutUnitsList\Manager;

use ScoutUnitsList\Exception\DbException;
use wpdb;

/**
 * Database statement manager
 */
class DbStatementManager
{
    /** @const string */
    const METHOD_DELETE = 'delete';

    /** @const string */
    const METHOD_INSERT = 'insert';

    /** @const string */
    const METHOD_PREPARE = 'prepare';

    /** @const string */
    const METHOD_REPLACE = 'replace';

    /** @const string */
    const METHOD_UPDATE = 'update';

    /** @const string */
    const OPTION_TYPE = 'type';

    /** @const string */
    const OPTION_VALUE = 'value';

    /** @var wpdb */
    protected $db;

    /** @var DbManager */
    protected $dbManager;

    /** @var string */
    protected $method;

    /** @var string */
    protected $queryOrTable;

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $conditions = [];

    /**
     * Constructor
     *
     * @param wpdb      $db           wordpress database
     * @param DbManager $dbManager    database manager
     * @param string    $method       method
     * @param string    $queryOrTable query or table
     */
    public function __construct($db, $dbManager, $method, $queryOrTable)
    {
        $this->db = $db;
        $this->dbManager = $dbManager;
        $this->method = $method;
        $this->queryOrTable = $queryOrTable;
    }

    /**
     * Set param
     *
     * @param string $key   key
     * @param mixed  $value value
     * @param string $type  type
     *
     * @return self
     */
    public function setParam($key, $value, $type = DbManager::TYPE_STRING)
    {
        $this->params[$key] = [
            self::OPTION_TYPE => isset($value) ? $type : null,
            self::OPTION_VALUE => $value,
        ];

        return $this;
    }

    /**
     * Set condition
     *
     * @param string $key   key
     * @param mixed  $value value
     * @param string $type  type
     *
     * @return self
     */
    public function setCondition($key, $value, $type = DbManager::TYPE_STRING)
    {
        $this->conditions[$key] = [
            self::OPTION_TYPE => isset($value) ? $type : null,
            self::OPTION_VALUE => $value,
        ];

        return $this;
    }

    /**
     * Get prepared query
     *
     * @return string
     *
     * @throws DbException
     */
    public function getQuery()
    {
        if ($this->method != self::METHOD_PREPARE) {
            throw new DbException('This method works only for "prepare" method.');
        }

        $arguments = [];
        $names = array_keys($this->params);
        $pattern = '#:(' . implode('|', $names) . ')#';

        $normalizedQuery = preg_replace_callback($pattern, function (array $matches) use (&$arguments) {
            $param = $this->params[$matches[1]];
            $type = $param[self::OPTION_TYPE];
            $value = $param[self::OPTION_VALUE];
            if (is_array($value)) {
                foreach ($value as $subvalue) {
                    $arguments[] = $subvalue;
                }
                $replacement = implode(', ', array_fill(0, count($value), $type));
            } else {
                $arguments[] = $value;
                $replacement = $type;
            }

            return $replacement;
        }, $this->queryOrTable);

        if (count($arguments) > 0) {
            array_unshift($arguments, $normalizedQuery);
            $preparedQuery = call_user_func_array([
                $this->db,
                'prepare',
            ], $arguments);
        } else {
            $preparedQuery = $normalizedQuery;
        }

        return $preparedQuery;
    }

    /**
     * Execute
     *
     * @return int
     */
    public function execute()
    {
        if ($this->method == self::METHOD_PREPARE) {
            $rowsNumber = $this->executePrepare($this->queryOrTable);
        } else {
            $rowsNumber = $this->executeOthers($this->method, $this->queryOrTable);
        }

        return $rowsNumber;
    }

    /**
     * Execute prepare
     *
     * @return int
     */
    private function executePrepare()
    {
        $query = $this->getQuery();
        $result = $this->dbManager->query($query);

        return $result;
    }

    /**
     * Execute others
     *
     * @param string $method method
     * @param string $table  table
     *
     * @return int
     *
     * @throws DbException
     */
    private function executeOthers($method, $table)
    {
        $params = [];
        $paramsFormat = [];
        foreach ($this->params as $name => $options) {
            $params[$name] = $options[self::OPTION_VALUE];
            $paramsFormat[] = $options[self::OPTION_TYPE];
        }

        $conditions = [];
        $conditionsFormat = [];
        foreach ($this->conditions as $name => $options) {
            $conditions[$name] = $options[self::OPTION_VALUE];
            $conditionsFormat[] = $options[self::OPTION_TYPE];
        }

        switch ($method) {
            case self::METHOD_DELETE:
                $result = $this->db->$method($table, $conditions, $conditionsFormat);
                break;

            case self::METHOD_INSERT:
            case self::METHOD_REPLACE:
                $result = $this->db->$method($table, $params, $paramsFormat);
                break;

            case self::METHOD_UPDATE:
                $result = $this->db->$method($table, $params, $conditions, $paramsFormat, $conditionsFormat);
                break;

            default:
                throw new DbException(sprintf('Method "%s" isn\'t available to use in statement.', $method));
        }

        if ($result === false) {
            throw new DbException(sprintf('An error occured during execution of "%s" method.', $method));
        }

        return $result;
    }
}
