<?php

namespace Morphable\SimpleDatabase;

use \PDO;
use \PDOStatement;
use \Morphable\SimpleDatabase\Connection;

class QueryExecuter
{
    /**
     * @var \PDOStatement
     */
    private $stmt;

    /**
     * @param \Morphable\SimpleDatabase\Connection $connection
     * @param string $query
     * @param array $prepares
     * @return self
     */
    public function __construct(Connection $connection, string $query, array $prepares = [])
    {
        $this->connection = $connection;
        $this->stmt = $this->connection->query($query, $prepares);
    }

    /**
     * @return array
     */
    public function fetch()
    {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function fetchOne()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return string
     */
    public function fetchColumn()
    {
        return $this->stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * @return \PDOStatement
     */
    public function getStatement()
    {
        return $this->stmt;
    }
}
