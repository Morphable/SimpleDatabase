<?php

namespace Morphable\SimpleDatabase;

use \PDO;
use \PDOStatement;
use \Morphable\SimpleDatabase\Executer;

class Builder
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $query;

    /**
     * @param string table name
     * @return self
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @return \PDOStatement
     */
    public function execute()
    {
    }
}
