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
            $dsn = 'sqlite:'.__DIR__.'/db/plugin_v1.sqlite';

            self::$conn = new PDO($dsn);

            if(!self::$conn) {
                throw new \RuntimeException(self::$conn->lastErrorMsg());
            }

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->exec("CREATE TABLE IF NOT EXISTS Page (facebookPageId INTEGER PRIMARY KEY, facebookToken TEXT, facebookPageName TEXT)");
            self::$conn->exec("CREATE TABLE IF NOT EXISTS Event (facebookEventId INTEGER PRIMARY KEY, facebookEventUpdateTime TEXT, facebookPageId INT, longitude TEXT, latitude TEXT, zip TEXT, street TEXT, city TEXT, state TEXT, place TEXT, cover TEXT, description TEXT, name TEXT, endTime TEXT,  startTime TEXT)");
        }
        return self::$conn;
    }
}
