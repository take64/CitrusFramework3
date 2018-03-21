<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

include_once dirname(__FILE__).'/Autoloader.class.php';

use Citrus\Configure\CitrusConfigureItem;
use Citrus\Document\CitrusDocumentRouter;

class CitrusConfigure
{
    /** @var string CitrusConfigureのデフォルトキー */
    const CONFIGURE_DEFAULT_KEY = 'default';


    /** @var CitrusConfigureItem */
    public static $CONFIGURE_ITEM = null;

    /** @var CitrusConfigureItem[] */
    public static $CONFIGURE_ITEMS = [];

    /** @var array plain configure default */
    public static $CONFIGURE_PLAIN_DEFAULT = [];

    /** @var array plain configure domain */
    public static $CONFIGURE_PLAIN_DOMAIN = [];

    /** @var string */
    public static $PATH_FRAMEWORK;

    /** @var string dir */
    public static $DIR_APP;

    /** @var string dir */
    public static $DIR_BUSINESS;

    /** @var string dir */
    public static $DIR_BUSINESS_CAPSULE;

    /** @var string dir */
    public static $DIR_BUSINESS_ENTITY;

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
    private static $IS_INITIALIZED_FRAMEWORK = false;

    /** @var bool */
    private static $IS_INITIALIZED_DIRECTORY = false;

    /** @var bool */
    private static $IS_INITIALIZED_CONFIGURE = false;



    /**
     * configure initilize
     *
     * @param string $path_configure
     */
    public static function initialize($path_configure)
    {
        // init autoload
        CitrusAutoloader::autoloadFramework();

        // framework initialize
        self::fremework();

        // directory initialize
        self::directory(dirname($path_configure));

        // configure initialize
        self::configure($path_configure);

        // init autoload
        CitrusAutoloader::autoloadApplication();
    }



    /**
     * fremework initialize
     */
    public static function fremework()
    {
        // is initialized
        if (self::$IS_INITIALIZED_FRAMEWORK === true)
        {
            return ;
        }

        // path framework
        self::$PATH_FRAMEWORK = dirname(__FILE__).'/Citrus.class.php';

        // citrus intialize
        Citrus::initialize();

        // initialized
        self::$IS_INITIALIZED_FRAMEWORK = true;
    }



    /**
     * directory initialize
     *
     * @param string $path_application_dir
     */
    public static function directory(string $path_application_dir)
    {
        // is initialized
        if (self::$IS_INITIALIZED_DIRECTORY === true)
        {
            return ;
        }

        // directory
        self::$DIR_APP                  = $path_application_dir;
        // dir business
        self::$DIR_BUSINESS             = self::$DIR_APP . '/Business';
        self::$DIR_BUSINESS_CAPSULE     = self::$DIR_BUSINESS . '/Capsule';
        self::$DIR_BUSINESS_ENTITY      = self::$DIR_BUSINESS . '/Entity';
        self::$DIR_BUSINESS_FORMMAP     = self::$DIR_BUSINESS . '/Formmap';
        self::$DIR_BUSINESS_SERVICE     = self::$DIR_BUSINESS . '/Service';
        // dir integration
        self::$DIR_INTEGRATION          = self::$DIR_APP . '/Integration';
        self::$DIR_INTEGRATION_PROPERTY = self::$DIR_INTEGRATION . '/Property';
        self::$DIR_INTEGRATION_DAO      = self::$DIR_INTEGRATION . '/Dao';
        self::$DIR_INTEGRATION_CONDITION= self::$DIR_INTEGRATION . '/Condition';
        self::$DIR_INTEGRATION_SQLMAP   = self::$DIR_INTEGRATION . '/Sqlmap';

        // initialized
        self::$IS_INITIALIZED_DIRECTORY = true;
    }



    /**
     * configure initialize
     *
     * @param string $path_configure
     */
    public static function configure(string $path_configure)
    {
        // is initialized
        if (self::$IS_INITIALIZED_CONFIGURE === true)
        {
            return ;
        }

        // 設定の読み込み
        $configures = include($path_configure);

        $default_configure = [];
        if (array_key_exists(self::CONFIGURE_DEFAULT_KEY, $configures) === true)
        {
            $default_configure = $configures[self::CONFIGURE_DEFAULT_KEY];
            unset($configures[self::CONFIGURE_DEFAULT_KEY]);

            self::$CONFIGURE_PLAIN_DEFAULT = $default_configure;
        }

        // 設定情報の生成
        foreach ($configures as $domain => $one)
        {
            self::$CONFIGURE_ITEMS[$domain] = new CitrusConfigureItem($default_configure, $one);
        }
        // httpアクセスの場合
        if (isset($_SERVER['HTTP_HOST']) === true)
        {
            $domain = $_SERVER['HTTP_HOST'];
            self::$CONFIGURE_ITEM = self::$CONFIGURE_ITEMS[$domain];
            self::$CONFIGURE_PLAIN_DOMAIN = $configures[$domain];
        }
        else
        {
            self::$CONFIGURE_ITEM = self::$CONFIGURE_ITEMS[array_keys(self::$CONFIGURE_ITEMS)[0]];
            $first_key = array_keys($configures)[0];
            self::$CONFIGURE_PLAIN_DOMAIN = $configures[$first_key];
        }

        // ルーティング処理初期化
        CitrusDocumentRouter::initialize($default_configure, $configures[$domain]);

        // 認証処理初期化
        CitrusAuthentication::initialize($default_configure, $configures[$domain]);

        // メッセージ処理初期化
        CitrusMessage::initialize($default_configure, $configures[$domain]);

        // ロガー処理
        CitrusLogger::initialize($default_configure, self::$CONFIGURE_PLAIN_DOMAIN);
    }



    /**
     * configure設定のマージ処理
     *
     * @param string     $configure_key      設定キー
     * @param array|null $configure_default  設定(デフォルト)
     * @param array|null $configure_addition 設定(追加設定)
     * @return array
     */
    public static function configureMerge(string $configure_key, array $configure_default = null, array $configure_addition = null)
    {
        // デフォルト設定
        $configure_default  = CitrusNVL::coalesceEmpty($configure_default, CitrusConfigure::$CONFIGURE_PLAIN_DEFAULT);
        $configure_addition = CitrusNVL::coalesceEmpty($configure_addition, CitrusConfigure::$CONFIGURE_PLAIN_DOMAIN);

        $configure = [];
        $configure = array_merge($configure, CitrusNVL::ArrayVL($configure_default, $configure_key, []));
        return array_merge($configure, CitrusNVL::ArrayVL($configure_addition, $configure_key, []));
    }
}
