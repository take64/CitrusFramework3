<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

/**
 * オブジェクトアクセサ
 */
class Accessor
{
    /**
     * オブジェクトプロパティ
     *
     * @return array
     */
    public function properties() : array
    {
        return get_object_vars($this);
    }



    /**
     * 汎用ゲッター
     *
     * @param string $key キー
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (true === isset($this->$key))
        {
            return $this->$key;
        }
        return null;
    }



    /**
     * 汎用セッター
     *
     * @param string $key   キー
     * @param mixed  $value 値
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->$key = $value;
    }



    /**
     * 汎用アダー
     *
     * @param string $key   キー
     * @param mixed  $value 値
     * @return void
     */
    public function add(string $key, $value): void
    {
        // 追加プロパティ
        $target = &$this->$key;
        if (true === is_null($target))
        {
            // 配列化
            $target = [];
            // 再起した方で追加処理する
            $this->add($key, $value);
            return;
        }

        // 追加値が非配列であれば配列化
        $value = (true === is_array($value) ? $value : [$value]);

        // 足し算で追加
        $target = ($target + $value);
    }



    /**
     * 汎用リムーバー
     *
     * @param array|string $key 削除キー(配列なら複数削除)
     * @return void
     */
    public function remove($key): void
    {
        // 配列なら再起
        if (true === is_array($key))
        {
            foreach ($key as $vl)
            {
                $this->remove($vl);
            }
        }

        // null化して削除
        $this->$key = null;
    }



    /**
     * 配列の内容を配置する
     *
     * @param array|null $array 配置したい配列
     * @return void
     */
    public function bind(array $array = null): void
    {
        $this->bindArray($array);
    }



    /**
     * 配列の内容を配置する
     *
     * @param array|null $array 配置したい配列
     * @return void
     */
    public function bindArray(array $array = null): void
    {
        // nullはスルー
        if (true === is_null($array))
        {
            return;
        }

        // 配置
        foreach ($array as $ky => $vl)
        {
            $this->$ky = $vl;
        }
    }



    /**
     * オブジェクトの内容を配置する
     *
     * @param mixed|null $object 配置したいオブジェクト
     * @return void
     */
    public function bindObject($object = null): void
    {
        // nullはスルー
        if (true === is_null($object))
        {
            return;
        }

        // 配列化して追加
        $this->bindArray(get_object_vars($object));
    }
}
