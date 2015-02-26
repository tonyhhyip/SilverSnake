<?php
/**
 * To escape parameter of SQL.
 * Current support type:
 * <ul>
 * <li>string</li>
 * <li>int</li>
 * <li>double</li>
 * <li>float</li>
 *
 * @param mixed $value The value to be sanitize.
 * @param string $type Data type of the value in the SQL statement.
 * @return string sanitized value which is ready for the SQL statement.
 * @version 0.1-dev
 */
function sql_escape($value, $type='string') {
    switch(strtolower($type)) {
        case 'string':
            $value = '\'' . addslashes($value) . '\'';
            break;
        case 'int':
            $value = intval($value);
            break;
        case 'double':
            $value = doubleval($value);
            break;
        case 'float':
            $value = floatval($value);
            break;
        default:
            throw new \InvalidArgumentException("Unknown Data Type: $type");
    }
    return $value;
}