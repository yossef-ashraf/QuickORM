<?php

namespace ORM\Connection;

class Connection
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['dbname']}";
        if (isset($config['port'])) {
            $dsn .= ";port={$config['port']}";
        }
        if (isset($config['charset'])) {
            $dsn .= ";charset={$config['charset']}";
        }

        $this->pdo = new \PDO($dsn, $config['user'], $config['password']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
