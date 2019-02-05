# Simple database component
A simple database component, easy to implement into any system

## Usage
Before using the library, I will tell you this is a very
limited ORM, I do this intentially because in my opinion it's
better to write raw sql when you have complicated queries. 

```php
<?php

use \Morphable\SimpleDatabase;

$db = new SimpleDatabase($dsn, $user, $pass, $pdoOptions, function ($e) {
    // custom callback on query error, for instance to log failures
    die("error has occured " . $e->getMessage());
});

// execute raw query, returns PDOStatement
$stmt = $db->query("select ?", "1");
$stmt->fetch(\PDO::FETCH_COLUMN) // 1

// select
$select = $db->builder($table)
    ->select('`col1`, `col2`, `col3`')
    ->join('table on table.col1 = tale.col2')
    ->where('`col1` = ? and `col2` = ?', [$param1, $param2])
    ->orderBy('`col1` DESC')
    ->limit(5)
    ->offset(5)
    ->execute(); // returns QueryExecuter

$select->fetch(); // all rows
$select->fetchOne(); // first row
$select->fetchColumn(); // only possible when you only select 1 column
$select->getStatement(); // returns \PDOStatement

// insert
$insertId = $db->builder($table)
    ->insert([
        'col1' => $col1,
        'col2' => $col2,
        'col3' => $col3,
    ])
    ->execute()
    ->getLastInsertId();

// update
$db->builder($table)
    ->update([
        'col1' => $col1,
    ])
    // throws exception when you don't have a where clause
    ->where('`id` = ?', $id)
    ->execute();

// delete
$db->builder($table)
    ->delete()
    // throws exception when you don't have a where clause
    ->where('`id` = ?', $id)
    ->execute();

```

## Contributing
- Follow PSR-2 and the .editorconfig
- Start namespaces with \Morphable\SimpleDatabase
- Make tests
