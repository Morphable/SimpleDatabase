<?php

namespace Morphable\SimpleDatabase;

use \Morphable\SimpleDatabase\QueryBuilder;

class QueryGenerator
{
    /**
     * @var \Morphable\SimpleDatabase\QueryBuilder
     */
    private $builder;

    /**
     * @param \Morphable\SimpleDatabase\QueryBuilder
     * @return self
     */
    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $sql = "";
        switch ($this->builder->type) {
            case QueryBuilder::SELECT:
                $sql .= "SELECT " . ($this->builder->select ?? "*") . " " . "FROM `{$this->builder->table}` ";
                break;

            case QueryBuilder::INSERT:
                $sql .= "INSERT INTO {$this->builder->table} (";
                foreach (array_keys($this->builder->insert) as $key) {
                    $sql .= "`{$key}`,";
                }
                $sql = rtrim($sql, ',');
                $sql .= ") VALUES (";
                foreach (array_values($this->builder->insert) as $value) {
                    $sql .= "{$value},";
                }
                $sql = rtrim($sql, ',');
                $sql .= ") ";
                break;

            case QueryBuilder::UPDATE:
                $sql .= "UPDATE {$this->builder->table} SET ";
                foreach ($this->builder->update as $key => $value) {
                    $sql .= "`$key` = $value,";
                }
                $sql = rtrim($sql, ',');
                break;

            case QueryBuilder::DELETE:
                $sql .= "DELETE FROM {$this->builder->table} ";
                break;
        }

        if ($this->builder->joins) $sql .= "INNER JOIN {$this->builder->joins} ";
        if ($this->builder->where) $sql .= "WHERE {$this->builder->where} ";
        if ($this->builder->orderBy) $sql .= "ORDER BY {$this->builder->orderBy} ";
        if ($this->builder->limit) $sql .= "LIMIT {$this->builder->limit} ";
        if ($this->builder->offset) $sql .= "OFFSET {$this->builder->offset} ";

        return $sql;
    }
}
