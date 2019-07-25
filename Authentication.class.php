<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Authentication\Database;
use Citrus\Authentication\Item;
use Citrus\Authentication\Protocol;
use Citrus\Session\SessionException;

class Authentication
{
    /** @var string 認証タイプ(データベース) */
    const TYPE_DATABASE = 'database';

    /** @var string セッション保存キー */
    const SESSION_KEY = 'authentication';

    /** @var string CitrusConfigureキー */
    const CONFIGURE_KEY = 'authentication';



    /** @var string 認証テーブル名 */
    public static $AUTHORIZE_TABLE_NAME = 'users';

    /** @var string token生成アルゴリズム */
    public static $TOKEN_ALGO = 'sha256';

    /** @var int ログイン維持時間(秒) */
    public static $KEEP_SECOND = (60 * 60 * 24);

    /** @var Protocol 認証タイプインスタンス */
    public static $INSTANCE = null;

    /** @var bool 初期化済み */
    public static $IS_INITIALIZED = false;



    /**
     * initialize authentication
     *
     * @param array $default_configure
     * @param array $configure_domain
     */
    public static function initialize(array $default_configure = [], array $configure_domain = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // 認証設定
        $configure = Configure::configureMerge(self::CONFIGURE_KEY, $default_configure, $configure_domain);

        // 認証設定はないが初期化する可能性がある
        // 複数設定できるまでifで処理
        if (empty($configure) === false && $configure['type'] === self::TYPE_DATABASE)
        {
            self::$INSTANCE = new Database();
        }

        // initialized
        self::$IS_INITIALIZED = true;
    }



    /**
     * 認証処理
     *
     * @param Item $item
     * @return bool ture:認証成功, false:認証失敗
     */
    public static function authorize(Item $item) : bool
    {
        if (is_null(self::$INSTANCE) === true)
        {
            return false;
        }

        return self::$INSTANCE->authorize($item);
    }



    /**
     * 認証解除処理
     *
     * @return bool ture:認証成功, false:認証失敗
     */
    public static function deauthorize() : bool
    {
        if (is_null(self::$INSTANCE) === true)
        {
            return false;
        }

        return self::$INSTANCE->deauthorize();
    }



    /**
     * 認証のチェック
     * 認証できていれば期間の延長
     *
     * @param Item|null $item
     * @return bool true:チェック成功, false:チェック失敗
     */
    public static function isAuthenticated(Item $item = null) : bool
    {
        if (is_null(self::$INSTANCE) === true)
        {
            return false;
        }

        return self::$INSTANCE->isAuthenticated($item);
    }



    /**
     * ログイントークンの生成
     *
     * @param string|null $key
     * @return string
     * @throws CitrusException
     * @throws SessionException
     */
    public static function generateToken(string $key = null) : string
    {
        // セッションが無効 もしくは 存在しない場合
        if (Session::status() !== PHP_SESSION_ACTIVE)
        {
            throw new SessionException('セッションが無効 もしくは 存在しません。');
        }

        // アルゴリズムチェック
        if (in_array(self::$TOKEN_ALGO, hash_algos()) === false)
        {
            throw new CitrusException('未定義のtoken生成アルゴリズムです。');
        }

        // tokenキー
        $key = NVL::NVL($key, Session::$sessionId);

        // token生成し返却
        return hash(self::$TOKEN_ALGO, $key);
    }



    /**
     * ログイン維持制限時間の生成
     *
     * @return string
     */
    public static function generateKeepAt() : string
    {
        return date('Y-m-d H:i:s', Citrus::$TIMESTAMP_INT + self::$KEEP_SECOND);
    }
}