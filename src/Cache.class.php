<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Cache\Engine;
use Citrus\Cache\Memcached;
use Citrus\Cache\Redis;
use Closure;

class Cache
{
    /** @var string cache engine redis */
    const ENGINE_REDIS = 'redis';

    /** @var string cache engine memcached */
    const ENGINE_MEMCACHED = 'memcached';

    /** @var string CitrusConfigureキー */
    const CONFIGURE_KEY = 'cache';



    /** @var Engine */
    protected static $INSTANCE = null;

    /** @var bool is initialized */
    public static $IS_INITIALIZED = false;



    /**
     * initialize cache
     *
     * @param array $default_configure
     * @param array $configure_domain
     */
    public static function initialize($default_configure = [], $configure_domain = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // configure
        $configure = Configure::configureMerge(self::CONFIGURE_KEY, $default_configure, $configure_domain);

        // configure empty
        if (empty($configure) === true)
        {
            return ;
        }

        // cache engine type select
        $engine = $configure['engine'];

        // cache engine instance
        $prefix = NVL::ArrayVL($configure, 'prefix', '');
        $expire = NVL::ArrayVL($configure, 'expire', 0);
        switch ($engine)
        {
            // redis
            case self::ENGINE_REDIS :
                self::$INSTANCE = new Redis($prefix, $expire);
                self::$INSTANCE->connect($configure['host'], $configure['port']);
                break;

            // redis
            case self::ENGINE_MEMCACHED :
                self::$INSTANCE = new Memcached($prefix, $expire);
                self::$INSTANCE->connect($configure['host'], $configure['port']);
                break;

            default:
        }

        // initialized
        self::$IS_INITIALIZED = true;
    }



    /**
     * 値の取得
     *
     * @param string $key
     * @return mixed
     */
    public static function call(string $key)
    {
        self::initialize();

        if (self::$IS_INITIALIZED === true)
        {
            return self::$INSTANCE->call($key);
        }
        return null;
    }



    /**
     * 値の設定
     *
     * @param mixed $key
     * @param mixed $value
     * @param int   $expire
     */
    public static function bind($key, $value, int $expire = 0)
    {
        self::initialize();

        if (self::$IS_INITIALIZED === true)
        {
            self::$INSTANCE->bind($key, $value, $expire);
        }
    }


    /**
     * 値の存在確認
     *
     * @param mixed $key
     * @return bool
     */
    public static function exists($key): bool
    {
        self::initialize();

        if (self::$IS_INITIALIZED === true)
        {
            return self::$INSTANCE->exists($key);
        }
        return false;
    }



    /**
     * 値の取得
     * 存在しない場合は値の設定ロジックを実行し、返却する
     *
     * @param mixed   $key
     * @param Closure $valueFunction
     * @param int     $expire
     * @return mixed
     */
    public static function callWithBind($key, Closure $valueFunction, int $expire = 0)
    {
        self::initialize();

        if (self::$IS_INITIALIZED === true)
        {
            return self::$INSTANCE->callWithBind($key, $valueFunction, $expire);
        }
        return $valueFunction();
    }
}