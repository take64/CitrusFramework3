<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Message\Item;

/**
 * メッセージ処理
 */
class Message extends Accessor
{
    /** messages key */
    const KEY_MESSAGES = 'messages';



    /** @var Item[] messages */
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
        $configure = Configure::configureMerge('message', $default_configure, $configure_domain);

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
     * @return Item[]
     */
    public static function callItems()
    {
        // init
        self::initialize();

        // セッションが正の場合
        if (self::isSession() === true)
        {
            self::$items = Session::$session->call(self::KEY_MESSAGES);
        }

        return self::$items;
    }



    /**
     * call message for tag
     *
     * @param string $tag
     * @return Item[]
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
     * @return Item[]
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
     * @return Item[]
     */
    public static function callMessages()
    {
        return self::callItemsForType(Item::TYPE_MESSAGE);
    }



    /**
     * call error
     *
     * @return Item[]
     */
    public static function callErrors()
    {
        return self::callItemsForType(Item::TYPE_ERROR);
    }



    /**
     * call success
     *
     * @return Item[]
     */
    public static function callSuccesses()
    {
        return self::callItemsForType(Item::TYPE_SUCCESS);
    }



    /**
     * call warning
     *
     * @return Item[]
     */
    public static function callWarnings()
    {
        return self::callItemsForType(Item::TYPE_WARNING);
    }



    /**
     * pop message for type
     *
     * @param string $type
     * @return Item[]
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
            Session::$session->regist(self::KEY_MESSAGES, self::$items);
        }

        return $result;
    }



    /**
     * pop message
     *
     * @return Item[]
     */
    public static function popMessages()
    {
        return self::popItemsForType(Item::TYPE_MESSAGE);
    }



    /**
     * pop error
     *
     * @return Item[]
     */
    public static function popErrors()
    {
        return self::popItemsForType(Item::TYPE_ERROR);
    }



    /**
     * pop success
     *
     * @return Item[]
     */
    public static function popSuccesses()
    {
        return self::popItemsForType(Item::TYPE_SUCCESS);
    }



    /**
     * pop warning
     *
     * @return Item[]
     */
    public static function popWarnings()
    {
        return self::popItemsForType(Item::TYPE_WARNING);
    }



    /**
     * regist message element
     *
     * @param Item $item
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
            Session::$session->regist(self::KEY_MESSAGES, self::$items);
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
        self::addItem(new Item($description, Item::TYPE_MESSAGE, $name, false, $tag));
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
        self::addItem(new Item($description, Item::TYPE_ERROR, $name, false, $tag));
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
        self::addItem(new Item($description, Item::TYPE_SUCCESS, $name, false, $tag));
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
        self::addItem(new Item($description, Item::TYPE_WARNING, $name, false, $tag));
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
            Session::$session->remove(self::KEY_MESSAGES);
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
            if ($item->tag === $tag)
            {
                unset($items[$ky]);
            }
        }
        self::$items = $items;

        // セッション利用の場合はセッションに登録
        if (self::isSession() === true)
        {
            Session::$session->regist(self::KEY_MESSAGES, self::$items);
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