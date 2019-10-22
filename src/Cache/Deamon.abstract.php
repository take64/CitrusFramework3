<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Cache;

use Memcached;
use Redis;

/**
 * Class CitrusCacheDeamon
 * サーバーデーモンタイプのキャッシュ
 *
 * @package Citrus\Cache
 */
abstract class Deamon implements Engine
{
    /** @var Redis|Memcached handler */
    public $handler;

    /** @var string host */
    public $host;

    /** @var int port */
    public $port;

    /** @var string prefix */
    public $prefix;

    /** @var int expire second */
    public $expire;



    /**
     * constructor.
     *
     * @param string $prefix
     * @param int    $expire
     */
    public function __construct(string $prefix = '', int $expire = 0)
    {
        $this->prefix = $prefix;
        $this->expire = $expire;
    }



    /**
     * destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }



    /**
     * connection
     *
     * @param string $host
     * @param int $port
     * @return mixed
     */
    public abstract function connect(string $host, int $port);



    /**
     * disconection
     */
    public abstract function disconnect();



    /**
     * 基本的にはドメイン付きのキーを返す
     *
     * prefix <= 'hogehoge.com'
     * key    <= 'productSummaries'
     * => hogehoge.com:productSummaries
     *
     * ドメインがない場合は :productSummaries となるが、ドメインなしを明示的にしたいので : は捨てない
     *
     * @param mixed $key
     * @prama bool  $with_hash
     * @return string
     */
    public function callPrefixedKey($key, $with_hash = false)
    {
        if (is_string($key) === false)
        {
            $key = serialize($key);
        }

        if ($with_hash === true)
        {
            $key = md5($key);
        }

        return sprintf('%s:%s', $this->prefix, $key);
    }
}