<?php
/**
 * phpredis version 3.1.3
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Cache;


use Citrus\CitrusException;
use Closure;
use Redis;
use RedisException;

class CitrusCacheRedis extends CitrusCacheDeamon
{
    /**
     * connection
     *
     * @param string $host
     * @param int $port
     * @return mixed
     */
    public function connect(string $host, int $port = 6379)
    {
        $this->handler = new Redis();
        $this->handler->connect($host, $port);
    }



    /**
     * disconection
     */
    public function disconnect()
    {
        if (is_null($this->handler) === false)
        {
            $this->handler->close();
        }
        $this->handler = null;
    }



    /**
     * 値の取得
     *
     * @param mixed $key
     * @return mixed
     */
    public function call($key)
    {
        // cache key
        $cache_key = $this->callPrefixedKey($key);

        // serialized value
        $serialized_value = $this->handler->get($cache_key);

        // unserialize and return
        return unserialize($serialized_value);
    }


    /**
     * 値の設定
     *
     * @param mixed $key
     * @param mixed $value
     * @param int   $expire
     * @throws CitrusCacheException|CitrusException
     */
    public function bind($key, $value, int $expire = 0)
    {
        try
        {
            // cache key
            $cache_key = $this->callPrefixedKey($key);

            // serialized value
            $serialized_value = serialize($value);

            // set value
            $result = $this->handler->set($cache_key, $serialized_value);
            if ($result === false)
            {
                throw new CitrusCacheException(sprintf('Redis::set に失敗しました。 message=%s', $this->handler->getLastError()));
            }

            // set exprire
            if ($expire > 0)
            {
                $this->handler->setTimeout($cache_key, $expire);
            }
        }
        catch (RedisException $e)
        {
            throw CitrusCacheException::convert($e);
        }
        catch (CitrusCacheException $e)
        {
            throw $e;
        }
    }


    /**
     * 値の存在確認
     *
     * @param mixed $key
     * @return bool
     */
    public function exists($key): bool
    {
        // cache key
        $cache_key = $this->callPrefixedKey($key);

        return $this->handler->exists($cache_key);
    }



    /**
     * 値の取得
     * 存在しない場合は値の設定ロジックを実行し、返却する
     *
     * @param mixed   $key
     * @param Closure $valueFunction
     * @param int     $expire
     * @return mixed
     * @throws CitrusCacheException
     * @throws CitrusException
     */
    public function callWithBind($key, Closure $valueFunction, int $expire = 0)
    {
        // あれば返却
        $exists = $this->exists($key);
        if ($exists === true)
        {
            return $this->call($key);
        }

        // 無ければ、ロジックを実行し、保存しておく
        $value = $valueFunction();
        if (is_null($value) === false)
        {
            $this->bind($key, $value, $expire);
        }

        return $value;
    }
}