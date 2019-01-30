<?php

namespace Morphable\SimpleDatabase;

use \PDOStatement;

class Executer
{
    private $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }
}
