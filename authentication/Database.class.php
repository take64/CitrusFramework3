<?php
/**
 * Database.class.php.
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

use Citrus\Citrus;
use Citrus\CitrusAuthentication;
use Citrus\CitrusLogger;
use Citrus\CitrusSession;
use Citrus\Query\CitrusQueryBuilder;

/**
 * このモジュールを利用する場合は以下の構成のテーブルが必要です
 *
CREATE TABLE IF NOT EXISTS users (
    user_id CHARACTER VARYING(32) NOT NULL,
    paswword CHARACTER VARYING(64) NOT NULL,
    token TEXT,
    keep_at TIMESTAMP WITHOUT TIME ZONE,
    status INTEGER DEFAULT 0 NOT NULL,
    registed_at TIMESTAMP WITHOUT TIME ZONE DEFAULT current_timestamp NOT NULL,
    modified_at TIMESTAMP WITHOUT TIME ZONE DEFAULT current_timestamp NOT NULL,
    rowid SERIAL NOT NULL,
    rev INTEGER DEFAULT 1 NOT NULL
);
COMMENT ON COLUMN users.user_id IS 'ユーザーID';
COMMENT ON COLUMN users.paswword IS 'パスワードハッシュ';
COMMENT ON COLUMN users.token IS 'アクセストークン';

ALTER TABLE users ADD CONSTRAINT pk_users PRIMARY KEY (user_id);
CREATE INDEX IF NOT EXISTS idx_users_user_id_token ON users (user_id, token);
 *
 */
class CitrusAuthenticationDatabase extends CitrusAuthenticationProtocol
{
    /**
     * 認証処理
     *
     * @param CitrusAuthenticationItem $item
     * @return bool ture:認証成功, false:認証失敗
     */
    public function authorize(CitrusAuthenticationItem $item) : bool
    {
        // ログインID、パスワード のどちらかが null もしくは 空文字 だった場合は認証失敗
        if (empty($item->user_id) === true || empty($item->password) === true)
        {
            return false;
        }

        // 対象テーブル
        $table_name = CitrusAuthentication::$AUTHORIZE_TABLE_NAME;

        // 対象ユーザーがいるか？
        $condition = new CitrusAuthenticationItem();
        $condition->user_id = $item->user_id;
        $result = (new CitrusQueryBuilder())->select($table_name, $condition)->execute();
        // いなければ認証失敗
        if (count($result) === 0)
        {
            return false;
        }

        // パスワード照合
        if (password_verify($item->password, $result[0]->password) === false)
        {
            return false;
        }

        // 認証情報の保存
        $item->token = CitrusAuthentication::generateToken();
        $item->keep_at = CitrusAuthentication::generateKeepAt();
        $item->password = null;

        // データベースに現在のトークンと保持期間の保存
        $condition = new CitrusAuthenticationItem();
        $condition->rowid = $result[0]->rowid;
        $condition->rev = $result[0]->rev;
        $result = (new CitrusQueryBuilder())->update($table_name, $item, $condition)->execute();
        CitrusSession::$session->regist(CitrusAuthentication::SESSION_KEY, $item);
        CitrusSession::commit();

        return true;
    }


    /**
     * 認証解除処理
     *
     * @return bool ture:認証成功, false:認証失敗
     */
    public function deauthorize() : bool
    {
        CitrusSession::$session->remove(CitrusAuthentication::SESSION_KEY);
        CitrusSession::commit();

        return true;
    }



    /**
     * 認証のチェック
     * 認証できていれば期間の延長
     *
     * @param CitrusAuthenticationItem|null $item
     * @return bool true:チェック成功, false:チェック失敗
     */
    public function isAuthenticated(CitrusAuthenticationItem $item = null) : bool
    {
        // 指定されない場合はsessionから取得
        if (is_null($item) === true)
        {
            $item = CitrusSession::$session->call(CitrusAuthentication::SESSION_KEY);
        }
        // 認証itemが無い
        if (is_null($item) === true)
        {
            CitrusLogger::debug('ログアウト:認証Itemが無い');
            CitrusLogger::debug(CitrusSession::$session);
            return false;
        }
        // ユーザーIDとトークン、認証期間があるか
        if (empty($item->user_id) === true || empty($item->token) === true || empty($item->keep_at) === true)
        {
            CitrusLogger::debug('ログアウト:ユーザIDが無い(user_id=%s)、もしくはトークンが無い(token=%s)、もしくはタイムアウト(keep_at=%s)',
                $item->user_id,
                $item->token,
                $item->keep_at
                );
            return false;
        }

        // すでに認証期間が切れている
        $keep_timestamp = strtotime($item->keep_at);
        $now_timestamp = time();
        if ($keep_timestamp < $now_timestamp)
        {
            CitrusLogger::debug('ログアウト:タイムアウト(%s) < 現在時間(%s)',
                $keep_timestamp,
                $now_timestamp
            );
            return false;
        }

        // 対象テーブル
        $table_name = CitrusAuthentication::$AUTHORIZE_TABLE_NAME;

        // まだ認証済みなので、認証期間の延長
        $authentic = new CitrusAuthenticationItem();
        $authentic->keep_at = CitrusAuthentication::generateKeepAt();
        $condition = new CitrusAuthenticationItem();
        $condition->user_id = $item->user_id;
        $condition->token = $item->token;
        // 更新
        $result = (new CitrusQueryBuilder())->update($table_name, $authentic, $condition)->execute();

        // 時間を延長
        /** @var CitrusAuthenticationItem $item */
        $item = CitrusSession::$session->call(CitrusAuthentication::SESSION_KEY);
        $item->keep_at = $authentic->keep_at;
        CitrusSession::$session->regist(CitrusAuthentication::SESSION_KEY, $item);
        CitrusSession::commit();

        return ($result > 0 ? true : false);
    }
}