<?php

/**
 * @package php\db
 * package php.db;
 * @file SQLException
 */

namespace php\db;

/**
 * Throw when any Exception in the database access.
 */
class SQLException extends \PDOException implements DriverException{
    /**
     * The driver specific error code.
     *
     * @var integer|string|null
     */
    private $errorCode;

    /**
     * The SQLSTATE of the driver.
     *
     * @var string|null
     */
    private $sqlState;

    /**
     * Constructor.
     *
     * @param \PDOException $exception The PDO exception to wrap.
     */
    public function __construct(\PDOException $exception)
    {
        parent::__construct($exception->getMessage(), 0, $exception);

        $this->code      = $exception->getCode();
        $this->errorInfo = $exception->errorInfo;
        $this->errorCode = isset($exception->errorInfo[1]) ? $exception->errorInfo[1] : $exception->getCode();
        $this->sqlState  = isset($exception->errorInfo[0]) ? $exception->errorInfo[0] : $exception->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLState() {
        return $this->sqlState;
    }
}

?>