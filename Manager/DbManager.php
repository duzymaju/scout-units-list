<?php

namespace ScoutUnitsList\Manager;

use ScoutUnitsList\Exception\DbException;
use wpdb;

/**
 * Database manager
 */
class DbManager
{
    /** @const string */
    const TYPE_DECIMAL = '%d';

    /** @const string */
    const TYPE_FLOAT = '%f';

    /** @const string */
    const TYPE_STRING = '%s';

    /** @var wpdb */
    protected $db;

    /**
     * Constructor
     *
     * @param wpdb $wpdb wordpress database
     */
    public function __construct(wpdb $wpdb)
    {
        $this->db = $wpdb;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->db->prefix;
    }

    /**
     * Prepare
     *
     * @param string $query query
     *
     * @return DbStatementManager
     */
    public function prepare($query)
    {
        $statement = new DbStatementManager($this->db, $this, DbStatementManager::METHOD_PREPARE, $query);

        return $statement;
    }

    /**
     * Query
     *
     * @param string $query query
     *
     * @return int
     *
     * @throws DbException
     */
    public function query($query)
    {
        if (false === $rowsNumber = $this->db->query($query)) {
            throw new DbException('An error occured during execution of query method.');
        }

        return $rowsNumber;
    }

    /**
     * Insert
     *
     * @param string $table table
     *
     * @return DbStatementManager
     */
    public function insert($table)
    {
        $statement = new DbStatementManager($this->db, $this, DbStatementManager::METHOD_INSERT, $table);

        return $statement;
    }

    /**
     * Replace
     *
     * @param string $table table
     *
     * @return DbStatementManager
     */
    public function replace($table)
    {
        $statement = new DbStatementManager($this->db, $this, DbStatementManager::METHOD_REPLACE, $table);

        return $statement;
    }

    /**
     * Update
     *
     * @param string $table table
     *
     * @return DbStatementManager
     */
    public function update($table)
    {
        $statement = new DbStatementManager($this->db, $this, DbStatementManager::METHOD_UPDATE, $table);

        return $statement;
    }

    /**
     * Delete
     *
     * @param string $table table
     *
     * @return DbStatementManager
     */
    public function delete($table)
    {
        $statement = new DbStatementManager($this->db, $this, DbStatementManager::METHOD_DELETE, $table);

        return $statement;
    }

    /**
     * Get variable
     *
     * @param string $query       query
     * @param int    $columnIndex column index
     * @param int    $rowIndex    row index
     *
     * @return string
     *
     * @throws DbException
     */
    public function getVariable($query, $columnIndex = 0, $rowIndex = 0)
    {
        $variable = $this->db->get_var($query, $columnIndex, $rowIndex);
        if ($variable === null) {
            throw new DbException('An error occured during execution of get_var method.');
        }

        return $variable;
    }

    /**
     * Get row
     *
     * @param string $query        query
     * @param string $outputFormat output format
     * @param int    $rowIndex     row index
     *
     * @return array|object
     *
     * @throws DbException
     */
    public function getRow($query, $outputFormat = OBJECT, $rowIndex = 0)
    {
        $this->validateOutputFormat($outputFormat);

        $row = $this->db->get_row($query, $outputFormat, $rowIndex);
        if ($row === null) {
            throw new DbException('An error occured during execution of get_row method.');
        }

        return $row;
    }

    /**
     * Get column
     *
     * @param string $query       query
     * @param int    $columnIndex column index
     *
     * @return array
     */
    public function getColumn($query, $columnIndex = 0)
    {
        return $this->db->get_col($query, $columnIndex);
    }

    /**
     * Get results
     *
     * @param string $query        query
     * @param string $outputFormat output format
     *
     * @return array|object
     *
     * @throws DbException
     */
    public function getResults($query, $outputFormat = OBJECT)
    {
        $this->validateOutputFormat($outputFormat);

        $results = $this->db->get_results($query, $outputFormat);
        if ($results === null) {
            throw new DbException('An error occured during execution of get_results method.');
        }

        return $results;
    }

    /**
     * Validate output format
     *
     * @param string $outputFormat output format
     *
     * @throws DbException
     */
    protected function validateOutputFormat($outputFormat)
    {
        $availableOutputFormats = [
            ARRAY_A,
            ARRAY_N,
            OBJECT,
            OBJECT_K,
        ];
        if (!in_array($outputFormat, $availableOutputFormats)) {
            throw new DbException('Output format should be one of ARRAY_A, ARRAY_N, OBJECT, OBJECT_K.');
        }
    }

    /**
     * Get auto increment
     *
     * @param string $tableName table name
     *
     * @return int
     *
     * @throws DbException
     */
    public function getAutoIncrement($tableName)
    {
        $tableStatus = $this->getRow('SHOW TABLE STATUS LIKE "' . $tableName . '"', ARRAY_A);
        if (!isset($tableStatus['Auto_increment'])) {
            throw new DbException(sprintf('AUTO_INCREMENT in table "%s" is not defined.', $tableName));
        }
        $autoIncrement = (integer) $tableStatus['Auto_increment'];

        return $autoIncrement;
    }

    /**
     * Escape
     *
     * @param string $text text
     *
     * @return string
     */
    public function escape($text)
    {
        return esc_sql($text);
    }

    /**
     * Escape for LIKE statements
     * Has to be use before escape method
     *
     * @param string $text text
     *
     * @return string
     */
    public function escapeLike($text)
    {
        return $this->db->esc_like($text);
    }
}
