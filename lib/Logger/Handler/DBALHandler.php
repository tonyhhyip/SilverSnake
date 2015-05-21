<?php

namespace SilverSnake\Logger\Handler;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use SilverSnake\Database\Database;

class DBALHandler extends AbstractProcessingHandler
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $sql;

    /**
     * @param string $id
     * @param string $sql
     * @param int $level
     */
    public function __construct($id, $sql, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        if (!Database::$state)
            Database::loadDataSource();

        $this->connection = Database::getConnection($id);
        $this->sql = $sql;
    }

    protected function write(array $record)
    {
        $stmt = $this->connection->prepare($this->sql);
        $record['datetime'] = $record['datetime']->format('U');
        foreach ($record as $key => $value) {
            $stmt->bindParam(':$key', $value);
        }
        $stmt->execute();
    }
}