<?php

namespace Morphable;

use \Morphable\SimpleDatabase\Connection;
use \Morphable\SimpleDatabase\Builder;

class SimpleDatabase
{
    private $connection;

    /**
     * @param string dsn
     * @param string user
     * @param string password
     * @param array options
     * @param callable callback
     * @return self
     */
    public function __construct(string $dsn, string $user = 'root', string $password = '', $options = null, $callback = null)
    {
        $this->connection = new Connection($dsn, $user, $password, $options, $callback);
    }

    /**
     * Execute raw query
     *
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = [])
    {
        return $this->connection->query($sql, $params);
    }

    /**
     * Create a new builder
     *
     * @param string table name
     * @return \Morphable\SimpleDatabase\Builder
     */
    public function getBuilder(string $table)
    {
        return new Builder($table);
    }
}
