<?php

namespace Models;

use PDO;

class Database implements DatabaseInterface
{

    private static $config = [
        'adapter' => 'mysql',
        'charset' => 'UTF8',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'hsk',
        'username' => 'root',
        'password' => 'root'
    ];

    private static $connection;
    private static $attributes = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    public static function get(): PDO
    {
        if (!self::$connection) {
            self::$connection = new PDO(
                self::$config['adapter'] . ':host=' . self::$config['host'] .
                ';dbname=' . self::$config['database'] .
                ';port=' . self::$config['port'] .
                ';charset=' . self::$config['charset'],
                self::$config['username'],
                self::$config['password'],
                self::$attributes
            );
        }
        return self::$connection;
    }

}