<?php

namespace SilverSnake\Database;


use Doctrine\DBAL\DriverManager;

abstract class Database
{
    /**
     * @var bool
     */
    public static $state = false;

    /**
     * @var array
     */
    private static $dataSource = array();

    public static function loadDataSource()
    {
        $content = file_get_contents(__DIR__ . '/../../config/dataSource.json', true);
        $content = (array)json_decode($content);
        foreach ($content as $id => $param) {
            self::addDataSource($id, $param);
        }
    }

    public static function getConnection($id)
    {
        return DriverManager::getConnection(self::$dataSource[$id]);
    }

    public static function addDataSource($id, array $param)
    {
        self::$dataSource[$id] = $param;
    }

    public static function updateDataSource($id, array $param)
    {
        self::$dataSource[$id] = $param;
    }

    public static function removeDataSource($id)
    {
        unset(self::$dataSource[$id]);
    }
}