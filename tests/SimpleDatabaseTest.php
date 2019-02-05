<?php

use \Morphable\SimpleDatabase;
use \Morphable\SimpleDatabase\Connection;
use \Morphable\SimpleDatabase\QueryBuilder;
use \Morphable\SimpleDatabase\QueryExecuter;

class SimpleDatabaseTest extends \PHPUnit\Framework\TestCase
{
    private function usage()
    {
        return;

        $db = new SimpleDatabase("sqlite:" . __DIR__ . '/data/test.db', null, null, null, function ($e) {
            die("an error has occured:\n" . $e->getMessage());
        });

        // PDOStatement
        $db->query("SELECT 1");

        // get one user
        $db->builder("users")
            ->where("`id` = ?", [$id])
            ->join("`rules` on `rules`.`id` = `users`.`roleId`")
            ->limit(1)
            ->execute()
            ->fetchOne();

        // insert user
        $db->builder("users")
            ->insert([
                "name" => "jon",
                "secondName" => "doe"
            ])
            ->execute()
            ->getLastId();

        // update user
        $db->builder("users")
            ->update([
                "name" => "noj"
            ])
            ->where("`id` = ? AND `name`", [$id, "jon"])
            ->execute();

        // delete user
        $db->builder("users")
            ->delete()
            ->where("`id` = ?", [$id])
            ->execute();

        // get pagination
        $db->builder("posts")
            ->select('`id`, `name`, `lastName`, `email`')
            ->orderBy("`dateAdded`")
            ->limit(10)
            ->offset("?", [$page])
            ->execute()
            ->fetchAll();
    }

    private function migrate($db)
    {
        $sql = "    create table users (
                        name varchar,
                        secondName varchar,
                        fullName varchar
                    );
        ";

        $db->query($sql);
    }

    public function testSimpleDatabaseConnection()
    {
        $dbPath = __DIR__ . '/data/test.db';
        unlink($dbPath);

        $db = new SimpleDatabase("sqlite:$dbPath", null, null, null, function ($e) {
            die("error has occured " . $e->getMessage());
        });

        $this->migrate($db);

        // test connection
        $data = $db->query("select 1 as test")->fetch(\PDO::FETCH_COLUMN);
        $this->assertSame($data, '1');

        $sql = $db->builder("users")
            ->insert([
                'name' => 'jon',
                'secondName' => 'doe',
                'fullName' => 'jon doe'
            ])
            ->execute();

        $data = $db->query("select name from users where name = ? limit 1", ["jon"])->fetch(\PDO::FETCH_COLUMN);
        $this->assertSame($data, 'jon');

        $sql = $db->builder("users")
            ->update([
                'name' => 'noj'
            ])
            ->where('name = ?', 'jon')
            ->execute();

        $data = $db->query("select name from users where name = ? limit 1", ["noj"])->fetch(\PDO::FETCH_COLUMN);
        $this->assertSame($data, 'noj');

        $db->builder('users')
            ->update([
                'fullName' => 'eod noj'
            ])
            ->where('fullName = ?', 'jon doe')
            ->execute();

        $data = $db->query('select fullname from users where fullname = ?', 'eod noj')->fetch(\PDO::FETCH_COLUMN);
        $this->assertSame($data, 'eod noj');

        $db->builder('users')
            ->delete()
            ->where('fullname = ?', 'eod noj')
            ->execute();

        $data = $db->query('select count(1) from users where fullname = ?', 'eod noj')->fetch(\PDO::FETCH_COLUMN);
        $this->assertSame($data, '0');

        foreach (range(0, 20) as $i) {
            $db->builder('users')
            ->insert([
                'name' => $i
            ])->execute();
        }

        $executer = $data = $db->builder('users')
            ->select('*')
            ->orderBy('name desc')
            ->limit(5)
            ->offset(5)
            ->execute();

        $executer->fetch();

        unlink($dbPath);
    }
}
