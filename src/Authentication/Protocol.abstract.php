<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Authentication;

abstract class Protocol
{
    /**
     * 認証処理
     *
     * @param Item $item
     * @return bool ture:認証成功, false:認証失敗
     */
    public abstract function authorize(Item $item) : bool;


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
     * @param Item|null $item
     * @return bool true:チェック成功, false:チェック失敗
     */
    public abstract function isAuthenticated(Item $item = null) : bool;
}