<?php
/**
 * @package php.db;
 */

namespace php\db;
use \php\lang\Exception;

class DBException extends Exception{
    /**
     * @param string $method
     *
     * @return \php\db\DBException
     */
    public static function notSupported($method) {
        return new self("Operation '$method' is not supported by platform.");
    }

    /**
     * @return \php\db\DBException
     */
    public static function invalidPlatformSpecified() {
        return new self(
            "Invalid 'platform' option specified, need to give an instance of ".
            "\\php\\db\\AbstractPlatform.");
    }

    /**
     * Returns a new instance for an invalid specified platform version.
     *
     * @param string $version        The invalid platform version given.
     * @param string $expectedFormat The expected platform version format.
     *
     * @return \php\db\DBException
     */
    public static function invalidPlatformVersionSpecified($version, $expectedFormat) {
        return new self(
            sprintf(
                'Invalid platform version "%s" specified. ' .
                'The platform version has to be specified in the format: "%s".',
                $version,
                $expectedFormat
            )
        );
    }

    /**
     * @return \php\db\DBException
     */
    public static function invalidPdoInstance() {
        return new self(
            "The 'pdo' option was used in DriverManager::getConnection() but no ".
            "instance of PDO was given."
        );
    }

    /**
     * @return \php\db\DBException
     */
    public static function driverRequired() {
        return new self("The options 'driver' or 'driverClass' are mandatory if no PDO ".
            "instance is given to DriverManager::getConnection().");
    }

    /**
     * @param Driver     $driver
     * @param \php\lang\Exception $driverEx
     * @param string     $sql
     * @param array      $params
     *
     * @return \php\db\DBException
     */
    public static function driverExceptionDuringQuery(Driver $driver, Exception $driverEx, $sql, array $params = array()) {
        $msg = "An exception occurred while executing '".$sql."'";
        if ($params) {
            $msg .= " with params " . self::formatParameters($params);
        }
        $msg .= ":\n\n".$driverEx->getMessage();

        if ($driver instanceof ExceptionConverterDriver && $driverEx instanceof DriverException) {
            return $driver->convertException($msg, $driverEx);
        }

        return new self($msg, 0, $driverEx);
    }

    /**
     * @param \php\db\Driver     $driver
     * @param \php\lang\Exception $driverEx
     *
     * @return \php\db\DBException
     */
    public static function driverException(Driver $driver, Exception $driverEx) {
        $msg = "An exception occured in driver: " . $driverEx->getMessage();

        if ($driver instanceof ExceptionConverterDriver && $driverEx instanceof DriverException) {
            return $driver->convertException($msg, $driverEx);
        }

        return new self($msg, 0, $driverEx);
    }

    /**
     * Returns a human-readable representation of an array of parameters.
     * This properly handles binary data by returning a hex representation.
     *
     * @param array $params
     *
     * @return string
     */
    private static function formatParameters(array $params) {
        return '[' . implode(', ', array_map(function ($param) {
            $json = @json_encode($param);

            if (! is_string($json) || $json == 'null' && is_string($param)) {
                // JSON encoding failed, this is not a UTF-8 string.
                return '"\x' . implode('\x', str_split(bin2hex($param), 2)) . '"';
            }

            return $json;
        }, $params)) . ']';
    }

    /**
     * @param string $wrapperClass
     *
     * @return \php\db\DBException
     */
    public static function invalidWrapperClass($wrapperClass) {
        return new self("The given 'wrapperClass' " . $wrapperClass . " has to be a ".
            "subtype of \\php\\db\\Connection.");
    }

    /**
     * @param string $driverClass
     *
     * @return \php\db\DBException
     */
    public static function invalidDriverClass($driverClass) {
        return new self("The given 'driverClass' ".$driverClass." has to implement the ".
            "\\php\\db\\Driver interface.");
    }

    /**
     * @param string $tableName
     *
     * @return \php\db\DBException
     */
    public static function invalidTableName($tableName) {
        return new self("Invalid table name specified: ".$tableName);
    }

    /**
     * @param string $tableName
     *
     * @return \php\db\DBException
     */
    public static function noColumnsSpecifiedForTable($tableName) {
        return new self("No columns specified for table ".$tableName);
    }

    /**
     * @return \php\db\DBException
     */
    public static function limitOffsetInvalid() {
        return new self("Invalid Offset in Limit Query, it has to be larger or equal to 0.");
    }

    /**
     * @param string $name
     *
     * @return \php\db\DBException
     */
    public static function typeExists($name) {
        return new self('Type '.$name.' already exists.');
    }

    /**
     * @param string $name
     *
     * @return \php\db\DBException
     */
    public static function unknownColumnType($name) {
        return new self('Unknown column type "'.$name.'" requested. Any Doctrine type that you use has ' .
            'to be registered with \\php\\db\\dataType\\Type::addType(). You can get a list of all the ' .
            'known types with \\php\\db\\dataType\\Type::getTypesMap(). If this error occurs during database ' .
            'introspection then you might have forgot to register all database types for a Doctrine Type. Use ' .
            'AbstractPlatform#registerDoctrineTypeMapping() or have your custom types implement ' .
            'Type#getMappedDatabaseTypes(). If the type name is empty you might ' .
            'have a problem with the cache or forgot some mapping information.'
        );
    }

    /**
     * @param string $name
     *
     * @return \php\db\DBException
     */
    public static function typeNotFound($name) {
        return new self('Type to be overwritten '.$name.' does not exist.');
    }

}