<?php
/**
 * Engine.interface.php.
 * 2017/09/16
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Cache
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Cache;


use Closure;

interface CitrusCacheEngine
{
    /**
     * 値の取得
     *
     * @param mixed $key
     * @return mixed
     */
    public function call($key);



    /**
     * 値の設定
     *
     * @param mixed $key
     * @param mixed $value
     * @param int   $expire
     */
    public function bind($key, $value, int $expire = 0);



    /**
     * 値の存在確認
     *
     * @param mixed $key
     * @return bool
     */
    public function exists($key) : bool;



    /**
     * 値の取得
     * 存在しない場合は値の設定ロジックを実行し、返却する
     *
     * @param mixed   $key
     * @param Closure $valueFunction
     * @param int     $expire
     * @return mixed
     */
    public function callWithBind($key, Closure $valueFunction, int $expire = 0);
}