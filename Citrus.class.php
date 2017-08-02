<?php
/**
 * Citrus.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  .
 * @license     http://www.besidesplus.net/
 */

namespace Citrus;


class Citrus
{
    /** @var bool */
    public static $INITIALIZED = false;

    /** @var integer */
    public static $TIMESTAMP_INT;

    /** @var string */
    public static $TIMESTAMP_CHAR14;

    /** @var string */
    public static $TIMESTAMP_FORMAT;

    /**
     * フレームワーク初期化
     *
     * @param string|null $path フレームワークディレクトリパス
     */
    public static function initialize(string $path = null)
    {
        // is initialized
        if (self::$INITIALIZED === true)
        {
            return ;
        }

        // timestamp
        self::$TIMESTAMP_INT    = $_SERVER['REQUEST_TIME'];
        self::$TIMESTAMP_CHAR14 = date('YmdHis',      self::$TIMESTAMP_INT);
        self::$TIMESTAMP_FORMAT = date('Y-m-d H:i:s', self::$TIMESTAMP_INT);

        // initialized
        self::$INITIALIZED = true;
    }
}