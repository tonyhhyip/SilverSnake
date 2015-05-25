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

    /**
     * @var array
     */
    private static $connection = array();


    public static function loadDataSource()
    {
        $content = file_get_contents(__DIR__ . '/../../config/dataSource.json', true);
        $content = (array)json_decode($content);
        foreach ($content as $id => $param) {
            self::addDataSource($id, $param);
        }
        self::$state = true;
    }

    /**
     * @param string $id
     *
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getConnection($id)
    {
        if (!array_key_exists($id, self::$connection))
            self::$connection[$id] = DriverManager::getConnection(self::$dataSource[$id]);
        return self::$connection[$id];
    }

    /**
     * @param string $id
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public static function getQueryBuilder($id)
    {
        $conn = self::getConnection($id);
        return $conn->createQueryBuilder();
    }

    /**
     * @param string $id
     * @param array $param
     */
    public static function addDataSource($id, array $param)
    {
        self::$dataSource[$id] = $param;
    }

    /**
     * @param string $id
     * @param array $param
     */
    public static function updateDataSource($id, array $param)
    {
        self::$dataSource[$id] = $param;
        unset(self::$connection[$id]);
    }

    /**
     * @param string $id
     */
    public static function removeDataSource($id)
    {
        unset(self::$dataSource[$id]);
        unset(self::$connection[$id]);
    }
}