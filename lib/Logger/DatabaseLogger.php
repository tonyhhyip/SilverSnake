<?php

namespace SilverSnake\Logger;


use Doctrine\DBAL\Query\QueryBuilder;

class DatabaseLogger
{
    /**
     * @var \Doctrine\DBAL\Query\QueryBuilder
     */
    private $query;

    /**
     * @param QueryBuilder $query
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }
}