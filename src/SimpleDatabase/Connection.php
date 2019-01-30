<?php

namespace Morphable\SimpleDatabase;

use \PDO;

class Connection
{
    /**
     * Database connection string
     *
     * @var string
     */
    private $dsn;

    /**
     * Database user
     *
     * @var string
     */
    private $user;

    /**
     * Database password
     *
     * @var string
     */
    private $password;

    /**
     * PDO options
     *
     * @var array
     */
    private $options;

    /**
     * Connection
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Error callback
     *
     * @var callable
     */
    private $callback;

    /**
     * @param string dsn
     * @param string user
     * @param string password
     * @param array options
     * @param callable callback
     * @return self
     */
    public function __construct(string $dsn, string $user, string $password, array $options = null, $callback = null)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->options = $options ?? $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->callback = $callback ?? function ($e) {
            die($e->getMessage());
        };
    }

    /**
     * Connect to database
     *
     * @return self
     */
    private function connect()
    {
        if ($this->pdo != null) return $this;

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->password, $this->options);
        } catch (\PDOException $e) {
            if (is_callable($this->callback)) {
                ($this->callback)($e);
            }
        }

        return $this;
    }

    /**
     * Execute a query
     *
     * @param string query string
     * @param array params
     * @param \PDOStatement
     */
    public function query($sql, $params = [])
    {
        $this->connect();
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute($params);
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollback();
            if (is_callable($this->callback)) {
                ($this->callback)($e);
            }
        }

        return $stmt;
    }
}
