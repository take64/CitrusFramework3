<?php
/**
 * Cache.class.php.
 * 2017/09/16
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


use Citrus\Cache\CitrusCacheEngine;
use Citrus\Cache\CitrusCacheMemcached;
use Citrus\Cache\CitrusCacheRedis;
use Closure;

class CitrusCache
{
    /** @var string cache engine redis */
    const ENGINE_REDIS = 'redis';

    /** @var string cache engine memcached */
    const ENGINE_MEMCACHED = 'memcached';




    /** @var CitrusCacheEngine */
    protected static $INSTANCE = null;

    /** @var bool is initialized */
    public static $IS_INITIALIZED = false;



    /**
     * initialize cache
     *
     * @param array $default_configure
     * @param array $configure
     */
    public static function initialize($default_configure = [], $configure = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
//            return self::$INSTANCE;?
        }

        // configure auto load
        $default_configure = CitrusNVL::coalesceEmpty($default_configure, CitrusConfigure::$CONFIGURE_PLAIN_DEFAULT);
        $configure = CitrusNVL::coalesceEmpty($configure, CitrusConfigure::$CONFIGURE_PLAIN_DOMAIN);

        // configure
        $cache = [];
        $cache = array_merge($cache, CitrusNVL::ArrayVL($default_configure, 'cache', []));
        $cache = array_merge($cache, CitrusNVL::ArrayVL($configure, 'cache', []));

        // configure empty
        if (empty($cache) === true)
        {
            return ;
        }

        // cache engine type select
        $engine = $cache['engine'];

        // cache engine instance
        switch ($engine)
        {
            // redis
            case self::ENGINE_REDIS :
                $prefix = CitrusNVL::ArrayVL($cache, 'prefix', '');
                $expire = CitrusNVL::ArrayVL($cache, 'expire', 0);
                self::$INSTANCE = new CitrusCacheRedis($prefix, $expire);
                self::$INSTANCE->connect($cache['host'], $cache['port']);
                break;

            // redis
            case self::ENGINE_MEMCACHED :
                $prefix = CitrusNVL::ArrayVL($cache, 'prefix', '');
                $expire = CitrusNVL::ArrayVL($cache, 'expire', 0);
                self::$INSTANCE = new CitrusCacheMemcached($prefix, $expire);
                self::$INSTANCE->connect($cache['host'], $cache['port']);
                break;
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