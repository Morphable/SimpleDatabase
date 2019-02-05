<?php

namespace Morphable;

use \Morphable\SimpleDatabase\Connection;
use \Morphable\SimpleDatabase\QueryBuilder;

class SimpleDatabase
{
    /**
     * @var \Morphable\SimpleDatabase\Connection
     */
    private $connection;

    /**
     * @param string dsn
     * @param string user
     * @param string password
     * @param array options
     * @param callable callback
     * @return self
     */
    public function __construct(string $dsn, string $user = null, string $password = null, $options = null, $callback = null)
    {
        $this->connection = new Connection($dsn, $user, $password, $options, $callback);
    }

    /**
     * Execute raw query
     * @param string sql
     * @param mixed params
     * @return \PDOStatement
     */
    public function query(string $query, $params = [])
    {
        if (!is_array($params)) {
            $params = [$params];
        }

        return $this->connection->query($query, $params);
    }

    /**
     * Create a new builder
     *
     * @param string table name
     * @return \Morphable\SimpleDatabase\QueryBuilder
     */
    public function builder(string $table)
    {
        return new QueryBuilder($this->connection, $table);
    }
}
