<?php

namespace php\db;


use php\lang\Exception;

abstract class Database {
    private static $connection = array();

    private static $dbConfig = array();

    private static $key = array();

    public static function addConnectionConfig(array $config) {
        if (count(array_diff(array_keys($config), array('host', 'username', 'password', 'driver'), 'dbname')))
            return null;
        $key = self::createKey($config);
        self::$dbConfig[$key] = $config;
        return self::storeKey($key);
    }

    public static function setConnectionConfig($key, array $config) {
        $key = self::searchKey($key);
        $conf = self::$dbConfig[$key];
        foreach ($config as $name => $value) {
            $conf[$name] = $value;
        }
        self::$dbConfig = $conf;
    }

    public static function removeConnectionConfigAttr($key, array $config) {
        $key = self::searchKey($key);
        foreach ($config as $name) {
            unset(self::$dbConfig[$key][$name]);
        }
    }

    public static function removeConnectionConfig($key) {
        $hash = self::searchKey($key);
        unset(self::$dbConfig[$hash]);
        self::removeKey($key);
    }

    protected static function createKey(array $config) {
         return sha1(serialize($config) . time());
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

    protected static function removeKey($key) {
        unset(self::$key[$key]);
    }

    public static function getConnection($key) {
        $key = self::searchKey($key);
        $config = self::$dbConfig[$key];
        if (isset(self::$connection[$key])) {
            return self::$connection[$key];
        }
        $class = '\\php\\db\\' . strtolower($config['driver']) . '\\' . ucfirst($config['driver']);
        import('php.db.' . $config['driver']);
        $conn = new $class;
        $conn->init($config);
        return self::$connection[$key];
    }

    public static function removeConnection($key) {
        unset(self::$dbConfig[$key]);
    }
}