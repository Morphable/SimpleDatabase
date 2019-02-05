<?php

namespace Morphable\SimpleDatabase;

use \PDOStatement;
use \Morphable\SimpleDatabase\Connection;
use \Morphable\SimpleDatabase\QueryExecuter;
use \Morphable\SimpleDatabase\QueryGenerator;

class QueryBuilder
{
    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const SELECT = 'SELECT';
    const DELETE = 'DELETE';

    /**
     * @var \Morphable\SimpleDatabase\Connection
     */
    private $connection;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $where;

    /**
     * @var string
     */
    public $select = '*';

    /**
     * @var array
     */
    public $update;

    /**
     * @var array
     */
    public $insert;

    /**
     * @var string
     */
    public $orderBy;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var string
     */
    public $joins;

    /**
     * Query builder is ment for simple queries
     * for advanced queries it's recommended to write it manually
     *
     * @param \Morphable\SimpleDatabase\Connection $connection
     * @param string $table
     * @return self
     */
    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->params = [];
        $this->type = self::SELECT;
    }

    /**
     * @param mixed $params
     * @return self
     */
    private function setParams($params)
    {
        if (!is_array($params)) {
            $this->params[] = $params;
            return $this;
        }

        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * @param string $select
     * @param mixed $params
     * @return self
     */
    public function select(string $select, $params = [])
    {
        $this->setParams($params);
        $this->select = $select;

        return $this;
    }

    /**
     * @param string $where
     * @param mixed $params
     * @return self
     */
    public function where(string $where, $params = [])
    {
        $this->setParams($params);
        $this->where = $where;

        return $this;
    }

    /**
     * @param array update
     * @return self
     */
    public function update(array $update)
    {
        $this->setParams(array_values($update));
        $this->update = array_combine(array_keys($update), array_fill(0, count($update), '?'));
        $this->type = self::UPDATE;

        return $this;
    }

    /**
     * @param array insert
     * @return self
     */
    public function insert(array $insert)
    {
        $this->setParams(array_values($insert));
        $this->insert = array_combine(array_keys($insert), array_fill(0, count($insert), '?'));
        $this->type = self::INSERT;

        return $this;
    }

    /**
     * @return self
     */
    public function delete()
    {
        $this->type = self::DELETE;

        return $this;
    }

    /**
     * @param string orderBy
     * @param mixed $params
     * @return self
     */
    public function orderBy(string $orderBy, $params = [])
    {
        $this->setParams($params);
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @param string limit
     * @param mixed $params
     * @return self
     */
    public function limit(string $limit, $params = [])
    {
        $this->setParams($params);
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string offset
     * @param mixed params
     * @return self
     */
    public function offset(string $offset, $params = [])
    {
        $this->setParams($params);
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string join
     * @param mixed params
     * @return self
     */
    public function join(string $join, $params = [])
    {
        $this->setParams($params);
        $this->joins = $joins;

        return $this;
    }

    /**
     * @return string
     */
    private function build()
    {
        if ($this->query != null) {
            return $this;
        }

        if (in_array($this->type, [self::UPDATE, self::DELETE]) && $this->where == null) {
            throw new Exception("Update or delete query without where??");
        }

        $generator = new QueryGenerator($this);
        $this->query = $generator->generate();

        return $this->query;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->build();
    }

    /**
     * @return \PDOStatement
     */
    public function execute()
    {
        return new QueryExecuter($this->connection, $this->build(), $this->params);
    }
}
