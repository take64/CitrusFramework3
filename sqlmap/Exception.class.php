<?php
/**
 * Exception.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     .
 * @subpackage  .
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;


use Citrus\CitrusException;
use PDOException;
use Throwable;

class CitrusSqlmapException extends CitrusException
{
    /**
     * constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }



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
//        var_dump($errorInfo);
//        $errorInfo[2] = '';
        return new CitrusSqlmapException($errorInfo[2], $errorInfo[0]);
    }
}