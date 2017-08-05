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
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

class Citrus
{
    /** @var string dir */
    public static $DIR_APP;

    /** @var string dir */
    public static $DIR_BUSINESS;

    /** @var string dir */
    public static $DIR_BUSINESS_FORMMAP;

    /** @var string dir */
    public static $DIR_BUSINESS_SERVICE;

    /** @var string dir */
    public static $DIR_INTEGRATION;

    /** @var string dir */
    public static $DIR_INTEGRATION_PROPERTY;

    /** @var string dir */
    public static $DIR_INTEGRATION_DAO;

    /** @var string dir */
    public static $DIR_INTEGRATION_CONDITION;

    /** @var string dir */
    public static $DIR_INTEGRATION_SQLMAP;


    /** @var bool */
    public static $IS_INITIALIZED = false;

    /** @var integer */
    public static $TIMESTAMP_INT;

    /** @var string */
    public static $TIMESTAMP_CHAR14;

    /** @var string */
    public static $TIMESTAMP_FORMAT;



    /**
     * フレームワーク初期化
     *
     * @param string $path_application_dir アプリケーションディレクトリ
     */
    public static function initialize(string $path_application_dir)
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // directory
        self::$DIR_APP                  = $path_application_dir;
        // dir business
        self::$DIR_BUSINESS             = self::$DIR_APP . '/Business';
        self::$DIR_BUSINESS_FORMMAP     = self::$DIR_BUSINESS . '/Formmap';
        self::$DIR_BUSINESS_SERVICE     = self::$DIR_BUSINESS . '/Service';
        // dir integration
        self::$DIR_INTEGRATION          = self::$DIR_APP . '/Integration';
        self::$DIR_INTEGRATION_PROPERTY = self::$DIR_INTEGRATION . '/Property';
        self::$DIR_INTEGRATION_DAO      = self::$DIR_INTEGRATION . '/Dao';
        self::$DIR_INTEGRATION_CONDITION= self::$DIR_INTEGRATION . '/Condition';
        self::$DIR_INTEGRATION_SQLMAP   = self::$DIR_INTEGRATION . '/Sqlmap';


        // timestamp
        self::$TIMESTAMP_INT    = $_SERVER['REQUEST_TIME'];
        self::$TIMESTAMP_CHAR14 = date('YmdHis',      self::$TIMESTAMP_INT);
        self::$TIMESTAMP_FORMAT = date('Y-m-d H:i:s', self::$TIMESTAMP_INT);

        // initialized
        self::$IS_INITIALIZED = true;
    }
}