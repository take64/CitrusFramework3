<?php
/**
 * Exception.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Session
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;


use Citrus\CitrusException;
use PDOException;

class CitrusSqlmapException extends CitrusException
{
    /**
     * PDO exception
     *
     * @param PDOException $e
     * @return CitrusSqlmapException
     */
    public static function pdoException(PDOException $e)
    {
        return new CitrusSqlmapException($e->getMessage(), $e->getCode());
    }



    /**
     * pdo errorInfo
     *
     * @param array $errorInfo
     * @return CitrusSqlmapException
     */
    public static function pdoErrorInfo(array $errorInfo)
    {
        return new CitrusSqlmapException($errorInfo[2], $errorInfo[0]);
    }
}