<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Configure\ConfigureException;
use Citrus\Configure\Item;
use Citrus\Document\Router;

/**
 * 設定
 */
class Configure
{
    /** @var string CitrusConfigureのデフォルトキー */
    const CONFIGURE_DEFAULT_KEY = 'default';

    /** @var array 設定ファイル内容 */
    public static $CONFIGURES = [];

    /** @var Item */
    public static $CONFIGURE_ITEM = null;

    /** @var Item[] */
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
        self::$CONFIGURES = include($path_configure);

        // framework initialize
        self::fremework();

        // directory initialize
        $app_path = self::$CONFIGURES['default']['application']['path'];
        self::directory($app_path);

        // configure initialize
        self::configure($path_configure);
    }



    /**
     * fremework initialize
     */
    public static function fremework()
    {
        // is initialized
        if (self::$IS_INITIALIZED_FRAMEWORK === true)
        {
            return;
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
            return;
        }

        // 親参照指定を取り除く
        $path_application_dir = Directory::suitablePath($path_application_dir);

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
     * @return void
     */
    public static function configure(string $path_configure): void
    {
        // is initialized
        if (self::$IS_INITIALIZED_CONFIGURE === true)
        {
            return;
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
            self::$CONFIGURE_ITEMS[$domain] = new Item($default_configure, $one);
        }
        // httpアクセスの場合
        if (isset($_SERVER['HTTP_HOST']) === true)
        {
            $domain = $_SERVER['HTTP_HOST'];
            self::$CONFIGURE_ITEM = self::$CONFIGURE_ITEMS[$domain];
            self::$CONFIGURE_PLAIN_DOMAIN = $configures[$domain];
        }
        // コマンドラインアクセスの場合
        else if (true === isset($_SERVER['argv']) && 0 < count($_SERVER['argv']))
        {
            $domain = '';
            $params = [];
            foreach ($_SERVER['argv'] as $one)
            {
                // パラメータじゃない
                if (false === strpos($one, '='))
                {
                    continue;
                }
                list($param_key, $param_val) = explode('=', $one);
                $param_key = str_replace('--', '', $param_key);
                $params[$param_key] = $param_val;
            }
            if (true === isset($params['domain']))
            {
                $domain = $params['domain'];
            }
            else
            {
                $domain = array_keys(self::$CONFIGURE_ITEMS)[0];
            }
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
        Router::initialize($default_configure, $configures[$domain]);

        // 認証処理初期化
        Authentication::initialize($default_configure, $configures[$domain]);

        // メッセージ処理初期化
        Message::initialize($default_configure, $configures[$domain]);

        // ロガー処理
        Logger::initialize(include($path_configure));
    }



    /**
     * configure設定のマージ処理
     *
     * @param string     $configure_key      設定キー
     * @param array|null $configure_default  設定(デフォルト)
     * @param array|null $configure_addition 設定(追加設定)
     * @return array
     */
    public static function configureMerge(string $configure_key, array $configure_default = null, array $configure_addition = null): array
    {
        // デフォルト設定
        $configure_default  = NVL::coalesceEmpty($configure_default, Configure::$CONFIGURE_PLAIN_DEFAULT);
        $configure_addition = NVL::coalesceEmpty($configure_addition, Configure::$CONFIGURE_PLAIN_DOMAIN);

        $configure = [];
        $configure = array_merge($configure, NVL::ArrayVL($configure_default, $configure_key, []));
        return array_merge($configure, NVL::ArrayVL($configure_addition, $configure_key, []));
    }
}
