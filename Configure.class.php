<?php
/**
 * Configure.class.php.
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


use Citrus\Authentication\CitrusAuthenticationDatabase;
use Citrus\Configure\CitrusConfigureItem;
use Citrus\Document\CitrusDocumentRouter;

class CitrusConfigure
{
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
        self::autoloadFramework();

        // framework initialize
        self::fremework();

        // directory initialize
        self::directory(dirname($path_configure));

        // configure initialize
        self::configure($path_configure);

        // init autoload
        self::autoloadApplication();
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
        if (array_key_exists('default', $configures) === true)
        {
            $default_configure = $configures['default'];
            unset($configures['default']);

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
        CitrusDocumentRouter::initialize($default_configure, $configures);

        // 認証処理初期化
        CitrusAuthentication::initialize($default_configure, $configures);
        // ロガー処理
//        CitrusLogger::initialize($default_configure, $configures);
    }


    /**
     * autoload framework
     *
     * @throws \Exception
     */
    public static function autoloadFramework()
    {
        spl_autoload_register(function($use_class_name) {

            $namespace_head = 'Citrus\\';
            if (strpos($use_class_name, $namespace_head) === false)
            {
                return ;
            }

            $use_class_name = str_replace('\\', '/', $use_class_name);
            $use_class_paths = explode('/', $use_class_name);

            $class_file_path = sprintf('%s/%s.class.php',
                dirname(__FILE__),
                str_replace('Citrus/', '', $use_class_name)
            );

            $package_name = array_shift($use_class_paths);
            $class_name = array_pop($use_class_paths);

            $class_file_name = str_replace(implode(array_merge([ $package_name ], $use_class_paths)), '', $class_name);
            // 対象ファイルが見つからない時はベースのCitrusクラス
            if ($class_file_name === '')
            {
                $class_file_name = 'Citrus';
            }

            $is_load_class = false;
            $extentions = [ 'class', 'interface', 'abstract', 'trait', 'enum' ];
            foreach ($extentions as $extention)
            {
                $class_file_path = sprintf('%s/%s%s.%s.php',
                    dirname(__FILE__),
                    strtolower(implode('/',  $use_class_paths) . '/'),
                    $class_file_name,
                    $extention
                );
                if (file_exists($class_file_path) === true)
                {
                    include_once $class_file_path;
                    $is_load_class = true;
                    break;
                }
            }

            if ($is_load_class === false)
            {
                $error_message = 'load faild = ' . $class_file_path;
                var_dump($error_message);
                // TODO:
                throw new \Exception($error_message);
            }
        });
    }



    /**
     * autoload application
     *
     * @throws \Exception
     */
    public static function autoloadApplication()
    {
        spl_autoload_register(function($use_class_name) {

            $namespace_application = ucfirst(self::$CONFIGURE_ITEM->application->id);
            $namespace_head = $namespace_application . '\\';
            if (strpos($use_class_name, $namespace_head) === false)
            {
                return ;
            }

            $use_class_name = str_replace('\\', '/', $use_class_name);
            $use_class_paths = explode('/', $use_class_name);

            // application base path
            $application_base_path = self::$CONFIGURE_ITEM->application->path;

            // search path
            foreach ($use_class_paths as $ky => $vl)
            {
                // application namespace は削除
                if ($vl === $namespace_application)
                {
                    unset($use_class_paths[$ky]);
                    continue;
                }
                // class 名の中の application namespace は置換
                if (strpos($vl, $namespace_application) !== false)
                {
                    $use_class_paths[$ky] = str_replace($namespace_application, '', $vl);
                }
            }

            // パスに含まれる重複文字列の除去
            $_use_class_paths = $use_class_paths;
            $_use_class = array_pop($_use_class_paths);
            foreach ($_use_class_paths as $one)
            {
                if (strpos($_use_class, $one) === 0)
                {
                    $_use_class = str_replace($one, '', $_use_class);
                }
            }
//            CitrusLogger::error('_use_class = %s', $_use_class);
            $_use_class_paths[] = $_use_class;
            $use_class_paths = $_use_class_paths;

            // パス確定
            $is_load_class = false;
            $extentions = [ 'class', 'interface', 'abstract', 'trait', 'enum' ];
            foreach ($extentions as $extention)
            {
                $class_file_path = sprintf('%s/%s.%s.php',
                    $application_base_path,
                    implode('/', $use_class_paths),
                    $extention
                );
                if (file_exists($class_file_path) === true)
                {
                    CitrusLogger::debug($class_file_path);
                    include_once $class_file_path;
                    $is_load_class = true;
                    break;
                }
            }

            if ($is_load_class === false)
            {
                $error_message = 'load faild = ' . $class_file_path;
                throw new CitrusException($error_message);
            }
        });
    }
}