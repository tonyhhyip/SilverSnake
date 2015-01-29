<?php

namespace php\db;


use php\lang\Exception;

abstract class Database {
    private static $connection = array();

    private static $dbConfig = array();

    private static $key = array();

    public static function addConnectionConfig(array $config) {
        $key = self::createKey($config);
        self::$dbConfig[$key] = $config;
        return self::storeKey($key);
    }

    public static function setConnectionConfig($key, array $config) {
        $key = self::searchKey($key);
        $conf = self::$dbConfig[$key];
    }

    protected static function createKey(array $config) {
         return sha1(serialize($config));
    }

    protected static function storeKey($key) {
        if (strlen($key) != 40)
            throw new Exception('Key Error');
        self::$key[substr($key, 0, 10)] = $key;
        return substr($key, 0, 10);
    }

    protected static function searchKey($key) {
        return self::$key[$key];
    }
}