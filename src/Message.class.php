<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Configure\Configurable;
use Citrus\Message\Item;
use Citrus\Variable\Singleton;
use Citrus\Variable\Structs;

/**
 * メッセージ処理
 */
class Message extends Configurable
{
    use Singleton;
    use Structs;

    /** セッションキー */
    const SESSION_KEY = 'messages';

    /** @var Item[] メッセージ配列 */
    private $items = [];



    /**
     * メッセージが1件でもあるかどうか
     *
     * @return bool
     */
    public function exists(): bool
    {
        return (0 < count($this->callItems()));
    }



    /**
     * メッセージの取得
     *
     * @return Item[]
     */
    public function callItems(): array
    {
        if (true === $this->isSession())
        {
            return (Session::$session->call(self::SESSION_KEY) ?? []);
        }
        return $this->items;
    }



    /**
     * メッセージの登録
     *
     * @param Item[] $items アイテム配列
     * @return void
     */
    public function registItems(array $items): void
    {
        if (true === $this->isSession())
        {
            Session::$session->regist(self::SESSION_KEY, $items);
        }
        $this->items = $items;
    }



    /**
     * タグでフィルタリングしてメッセージを取得する
     *
     * @param string $tag
     * @return Item[]
     */
    public function callItemsOfTag(string $tag): array
    {
        return Collection::stream($this->callItems())->filter(function ($ky, $vl) use ($tag) {
            // タグが一致しているかどうか
            /** @var Item $vl */
            return ($vl->tag === $tag);
        })->toList();
    }



    /**
     * タイプでフィルタリングしてメッセージを取得する
     *
     * @param string $type
     * @return Item[]
     */
    public function callItemsOfType(string $type): array
    {
        return Collection::stream($this->callItems())->filter(function ($ky, $vl) use ($type) {
            // タイプが一致しているかどうか
            /** @var Item $vl */
            return ($vl->type === $type);
        })->toList();
    }



    /**
     * メッセージを取得
     *
     * @return Item[]
     */
    public function callMessages(): array
    {
        return $this->callItemsOfType(Item::TYPE_MESSAGE);
    }



    /**
     * エラーメッセージを取得
     *
     * @return Item[]
     */
    public function callErrors(): array
    {
        return $this->callItemsOfType(Item::TYPE_ERROR);
    }



    /**
     * 成功メッセージを取得
     *
     * @return Item[]
     */
    public function callSuccesses(): array
    {
        return $this->callItemsOfType(Item::TYPE_SUCCESS);
    }



    /**
     * 警告メッセージを取得
     *
     * @return Item[]
     */
    public function callWarnings(): array
    {
        return $this->callItemsOfType(Item::TYPE_WARNING);
    }



    /**
     * メッセージをポップする
     *
     * @param string $type
     * @return Item[]
     */
    public function popItemsForType(string $type): array
    {
        // 結果
        $results = [];

        // 走査
        $items = $this->callItems();
        foreach ($items as $ky => $vl)
        {
            // タイプの合うものだけ取得して削除
            if ($vl->type === $type)
            {
                $results[] = $vl;
                unset($items[$ky]);
            }
        }

        // 再設定
        $this->registItems($items);

        return $results;
    }



    /**
     * メッセージを取得して削除
     *
     * @return Item[]
     */
    public function popMessages(): array
    {
        return $this->popItemsForType(Item::TYPE_MESSAGE);
    }



    /**
     * エラーメッセージを取得して削除
     *
     * @return Item[]
     */
    public function popErrors(): array
    {
        return $this->popItemsForType(Item::TYPE_ERROR);
    }



    /**
     * 成功メッセージを取得して削除
     *
     * @return Item[]
     */
    public function popSuccesses(): array
    {
        return $this->popItemsForType(Item::TYPE_SUCCESS);
    }



    /**
     * 警告メッセージを取得して削除
     *
     * @return Item[]
     */
    public function popWarnings(): array
    {
        return $this->popItemsForType(Item::TYPE_WARNING);
    }



    /**
     * メッセージアイテムの設定
     *
     * @param Item $item
     * @return void
     */
    public function addItem(Item $item): void
    {
        // 取得
        $items = $this->callItems();

        // 追加
        $items[] = $item;

        // 再設定
        $this->registItems($items);
    }



    /**
     * メッセージ追加
     *
     * @param string      $description 内容
     * @param string|null $name        名称
     * @param string|null $tag         タグ
     * @return void
     */
    public function addMessage(string $description, string $name = null, string $tag = null): void
    {
        $this->addItem(new Item($description, Item::TYPE_MESSAGE, $name, false, $tag));
    }



    /**
     * エラーメッセージの追加
     *
     * @param string      $description 内容
     * @param string|null $name        名称
     * @param string|null $tag         タグ
     * @return void
     */
    public function addError(string $description, string $name = null, string $tag = null): void
    {
        $this->addItem(new Item($description, Item::TYPE_ERROR, $name, false, $tag));
    }



    /**
     * 成功メッセージの追加
     *
     * @param string      $description 内容
     * @param string|null $name        名称
     * @param string|null $tag         タグ
     * @return void
     */
    public function addSuccess(string $description, string $name = null, string $tag = null): void
    {
        $this->addItem(new Item($description, Item::TYPE_SUCCESS, $name, false, $tag));
    }



    /**
     * 警告メッセージの追加
     *
     * @param string      $description 内容
     * @param string|null $name        名称
     * @param string|null $tag         タグ
     * @return void
     */
    public function addWarning(string $description, string $name = null, string $tag = null): void
    {
        $this->addItem(new Item($description, Item::TYPE_WARNING, $name, false, $tag));
    }



    /**
     * メッセージの全削除
     *
     * @return void
     */
    public function removeAll(): void
    {
        // プロパティから削除
        $this->items = [];

        // セッションから削除
        if (true === $this->isSession())
        {
            Session::$session->remove(self::SESSION_KEY);
        }
    }



    /**
     * メッセージのタグごと削除
     *
     * @param string|null $tag
     * @return void
     */
    public function removeOfTag(string $tag = null): void
    {
        // 削除後メッセージを取得
        $items = Collection::stream($this->callItems())->remove(function ($ky, $vl) use ($tag) {
            // タグが一致しているかどうか(一致しているものが削除対象)
            /** @var Item $vl */
            return ($vl->tag === $tag);
        })->toList();

        // 再設定
        $this->registItems($items);
    }



    /**
     * メッセージのタイプごと削除
     *
     * @param string|null $type
     * @return void
     */
    public function removeOftype(string $type = null): void
    {
        // 削除後メッセージを取得
        $items = Collection::stream($this->callItems())->remove(function ($ky, $vl) use ($type) {
            // タイプが一致しているかどうか(一致しているものが削除対象)
            /** @var Item $vl */
            return ($vl->tag === $type);
        })->toList();

        // 再設定
        $this->registItems($items);
    }



    /**
     * セッションを使うかどうか
     *
     * @return bool true:セッションを使う
     */
    public function isSession(): bool
    {
        return $this->configures['enable_session'];
    }



    /**
     * {@inheritDoc}
     */
    protected function configureKey(): string
    {
        return 'message';
    }



    /**
     * {@inheritDoc}
     */
    protected function configureDefaults(): array
    {
        return [
            'enable_session' => true,
        ];
    }



    /**
     * {@inheritDoc}
     */
    protected function configureRequires(): array
    {
        return [
            'enable_session',
        ];
    }
}
