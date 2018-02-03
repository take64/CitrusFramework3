<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Message\CitrusMessageItem;

class CitrusMessage extends CitrusClass
{
    /** @var CitrusMessageItem[] messages */
    public static $items = [];

    /** @var bool is initialized */
    public static $IS_INITIALIZED = false;

    /** @var bool enable session */
    public static $enable_session = false;



    /**
     * initialize message
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

        // メッセージ設定
        $configure = CitrusConfigure::configureMerge('message', $default_configure, $configure_domain);

        self::$enable_session = $configure['enable_session'];

        // initialized
        self::$IS_INITIALIZED = true;
    }



    /**
     * exists message
     *
     * @return  bool
     */
    public static function exists()
    {
        $items = self::callItems();
        if (is_array($items) === false)
        {
            return false;
        }
        return (count($items) > 0);
    }



    /**
     * call message
     *
     * @return CitrusMessageItem[]
     */
    public static function callItems()
    {
        // init
        self::initialize();

        // セッションが正の場合
        if (self::isSession() === true)
        {
            self::$items = CitrusSession::$session->call('messages');
        }

        return self::$items;
    }



    /**
     * call message for tag
     *
     * @param string $tag
     * @return CitrusMessageItem[]
     */
    public static function callItemsForTag($tag = null)
    {
        // 結果
        $result = [];

        // 引数なし
        if (empty($tag) === true)
        {
            return $result;
        }

        // エイリアス
        $items = self::$items;

        // 走査
        foreach ($items as $item)
        {
            if ($item->tag === $tag)
            {
                $result[] = $item;
            }
        }

        return $result;
    }



    /**
     * call message for type
     *
     * @param string $type
     * @return CitrusMessageItem[]
     */
    public static function callItemsForType($type = null)
    {
        // 結果
        $result = [];

        // 引数なし
        if (is_null($type) === true)
        {
            return $result;
        }

        // 走査
        foreach (self::$items as $item)
        {
            if ($item->type === $type)
            {
                $result[] = $item;
            }
        }

        return $result;
    }



    /**
     * call message
     *
     * @return CitrusMessageItem[]
     */
    public static function callMessages()
    {
        return self::callItemsForType(CitrusMessageItem::TYPE_MESSAGE);
    }



    /**
     * call error
     *
     * @return CitrusMessageItem[]
     */
    public static function callErrors()
    {
        return self::callItemsForType(CitrusMessageItem::TYPE_ERROR);
    }



    /**
     * call success
     *
     * @return CitrusMessageItem[]
     */
    public static function callSuccesses()
    {
        return self::callItemsForType(CitrusMessageItem::TYPE_SUCCESS);
    }



    /**
     * call warning
     *
     * @return CitrusMessageItem[]
     */
    public static function callWarnings()
    {
        return self::callItemsForType(CitrusMessageItem::TYPE_WARNING);
    }



    /**
     * pop message for type
     *
     * @param string $type
     * @return CitrusMessageItem[]
     */
    public static function popItemsForType(string $type = null)
    {
        // 結果
        $result = [];

        // 引数なし
        if (is_null($type) === true)
        {
            return $result;
        }

        // 走査
        foreach (self::$items as $ky => $item)
        {
            if ($item->type === $type)
            {
                $result[] = $item;
                unset(self::$items[$ky]);
            }
        }

        // セッション利用
        if (self::isSession() === true)
        {
            CitrusSession::$session->regist('messages', self::$items);
        }

        return $result;
    }



    /**
     * pop message
     *
     * @return CitrusMessageItem[]
     */
    public static function popMessages()
    {
        return self::popItemsForType(CitrusMessageItem::TYPE_MESSAGE);
    }



    /**
     * pop error
     *
     * @return CitrusMessageItem[]
     */
    public static function popErrors()
    {
        return self::popItemsForType(CitrusMessageItem::TYPE_ERROR);
    }



    /**
     * pop success
     *
     * @return CitrusMessageItem[]
     */
    public static function popSuccesses()
    {
        return self::popItemsForType(CitrusMessageItem::TYPE_SUCCESS);
    }



    /**
     * pop warning
     *
     * @return CitrusMessageItem[]
     */
    public static function popWarnings()
    {
        return self::popItemsForType(CitrusMessageItem::TYPE_WARNING);
    }



    /**
     * regist message element
     *
     * @param CitrusMessageItem $item
     */
    public static function addItem($item)
    {
        // init
        self::initialize();

        // 既に配列の場合は追加
        if (is_array(self::$items) === false)
        {
            self::$items = [];
        }
        self::$items[] = $item;

        // セッション利用
        if (self::isSession() === true)
        {
            CitrusSession::$session->regist('messages', self::$items);
        }
    }



    /**
     * add message
     *
     * @param string      $description
     * @param string|null $name
     * @param string|null $tag
     */
    public static function addMessage(string $description, string $name = null, $tag = null)
    {
        self::addItem(new CitrusMessageItem($description, CitrusMessageItem::TYPE_MESSAGE, $name, false, $tag));
    }


    
    /**
     * add error
     *
     * @param string      $description
     * @param string|null $name
     * @param string|null $tag
     */
    public static function addError(string $description, string $name = null, $tag = null)
    {
        self::addItem(new CitrusMessageItem($description, CitrusMessageItem::TYPE_ERROR, $name, false, $tag));
    }



    /**
     * add success
     *
     * @param string      $description
     * @param string|null $name
     * @param string|null $tag
     */
    public static function addSuccess(string $description, string $name = null, $tag = null)
    {
        self::addItem(new CitrusMessageItem($description, CitrusMessageItem::TYPE_SUCCESS, $name, false, $tag));
    }



    /**
     * add warning
     *
     * @param string      $description
     * @param string|null $name
     * @param string|null $tag
     */
    public static function addWarning(string $description, string $name = null, $tag = null)
    {
        self::addItem(new CitrusMessageItem($description, CitrusMessageItem::TYPE_WARNING, $name, false, $tag));
    }



    /**
     * remove message
     */
    public static function removeAll()
    {
        // init
        self::initialize();

        // クラス変数から削除
        self::$items = [];

        // セッションから削除
        if (self::isSession() === true)
        {
            CitrusSession::$session->remove('messages');
        }
    }



    /**
     * delete message
     *
     * @param string|null $tag
     */
    public static function removeForTag(string $tag = null)
    {
        // タグ指定がない場合は戻る
        if (empty($tag) === true)
        {
            return ;
        }

        // init
        self::initialize();

        // メッセージがない場合。
        if (empty(self::$items) === true)
        {
            return ;
        }

        // エイリアス
        $items = self::$items;

        // タグにマッチするメッセージの削除
        foreach ($items as $ky => $item)
        {
            if ($item->tag == $tag)
            {
                unset($items[$ky]);
            }
        }
        self::$items = $items;

        // セッション利用の場合はセッションに登録
        if (self::isSession() === true)
        {
            CitrusSession::$session->regist('messages', self::$items);
        }
    }



    /**
     * using session ?
     *
     * @return  bool
     */
    public static function isSession()
    {
        return self::$enable_session;
    }
}