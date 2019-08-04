<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Cache;


use Citrus\CitrusException;
use Closure;
use MemcachedException;

class Memcached extends Deamon
{
    /**
     * connection
     *
     * @param string $host
     * @param int $port
     * @return mixed
     */
    public function connect(string $host, int $port = 11211)
    {
        $this->handler = new \Memcached();
        $this->handler->addServer($host, $port);
    }



    /**
     * disconection
     */
    public function disconnect()
    {
        if (is_null($this->handler) === false)
        {
            $this->handler->quit();
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
        $cache_key = $this->callPrefixedKey($key, true);

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
     * @throws CacheException|CitrusException
     */
    public function bind($key, $value, int $expire = 0)
    {
        try
        {
            // cache key
            $cache_key = $this->callPrefixedKey($key, true);

            // serialized value
            $serialized_value = serialize($value);

            // expire
            if ($expire === 0)
            {
                $expire = $this->expire;
            }
            $expire += time();

            // set value
            $result = $this->handler->set($cache_key, $serialized_value, $expire);
            if ($result === false)
            {
                throw new CacheException(sprintf('Memcached::set に失敗しました。 message=%s', $this->handler->getResultMessage()), $this->handler->getResultCode());
            }
        }
        catch (MemcachedException $e)
        {
            throw CacheException::convert($e);
        }
        catch (CacheException $e)
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
        // 一旦キー取得(キーがあるかどうかで判断、取得するとステータスが発生する)
        $this->call($key);

        return \Memcached::RES_NOTFOUND !== $this->handler->getResultCode();
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
    public function callWithBind($key, Closure $valueFunction, int $expire = 0)
    {
        $exists = $this->exists($key);

        // あれば返却
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