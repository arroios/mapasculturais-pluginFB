<?php

namespace arroios\plugins;

use PDO;

class Database
{
    private static $conn;

    private function __construct()
    {
    }

    public static function getConnection()
    {
        if (self::$conn === NULL) {
            $dsn = 'sqlite:'.__DIR__.'/db/plugin.sqlite';

            self::$conn = new PDO($dsn);

            if(!self::$conn) {
                throw new \RuntimeException(self::$conn->lastErrorMsg());
            }

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->exec("CREATE TABLE IF NOT EXISTS Page (id INTEGER PRIMARY KEY, token TEXT, userId INT)");
            self::$conn->exec("CREATE TABLE IF NOT EXISTS Event (id INTEGER PRIMARY KEY, updateTime TEXT, pageId INT)");
        }
        return self::$conn;
    }
}
