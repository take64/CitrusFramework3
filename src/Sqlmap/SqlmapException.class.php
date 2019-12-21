<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\CitrusException;
use PDOException;

/**
 * Sqlmap用例外
 */
class SqlmapException extends CitrusException
{
    /**
     * PDO exception
     *
     * @param PDOException $e
     * @return SqlmapException
     */
    public static function pdoException(PDOException $e)
    {
        return new SqlmapException($e->getMessage(), $e->getCode());
    }



    /**
     * pdo errorInfo
     *
     * @param array $errorInfo
     * @return SqlmapException
     */
    public static function pdoErrorInfo(array $errorInfo)
    {
        return new SqlmapException(sprintf('[%s] %s', $errorInfo[0], $errorInfo[2]), $errorInfo[1]);
    }
}
