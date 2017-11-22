<?php
/**
 * Protocol.abstract.php.
 * 2017/08/10
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Authentication
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Authentication;


abstract class CitrusAuthenticationProtocol
{
    /**
     * 認証処理
     *
     * @param CitrusAuthenticationItem $item
     * @return bool ture:認証成功, false:認証失敗
     */
    public abstract function authorize(CitrusAuthenticationItem $item) : bool;


    /**
     * 認証解除処理
     *
     * @return bool ture:認証成功, false:認証失敗
     */
    public abstract function deauthorize() : bool;



    /**
     * 認証のチェック
     * 認証できていれば期間の延長
     *
     * @param CitrusAuthenticationItem|null $item
     * @return bool true:チェック成功, false:チェック失敗
     */
    public abstract function isAuthenticated(CitrusAuthenticationItem $item = null) : bool;
}