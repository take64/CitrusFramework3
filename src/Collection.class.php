<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

/**
 * コレクションクラス
 */
class Collection
{
    /** @var iterable データソース */
    protected $source;



    /**
     * constructor.
     *
     * @param array $source
     */
    public function __construct(array $source)
    {
        $this->source = $source;
    }



    /**
     * ソース配列の設定
     *
     * @param array $source
     * @return self
     */
    public static function stream(array $source): self
    {
        return new Collection($source);
    }



    /**
     * 出力
     *
     * @return array
     */
    public function toList(): array
    {
        return $this->source;
    }



    /**
     * 両方の要素を残したいい感じの配列マージ
     *
     * @param array $values
     * @return self
     */
    public function betterMerge(array $values): self
    {
        $this->source = self::betterMergeRecursive($this->source, $values);
        return $this;
    }



    /**
     * 両方の要素を残したいい感じの配列マージ
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private static function betterMergeRecursive(array $array1, array $array2): array
    {
        foreach ($array2 as $ky => $vl)
        {
            $array1[$ky] = (
            true === is_array($vl)
                ? self::betterMergeRecursive($array1[$ky], $array2[$ky]) // 配列の場合
                : $array2[$ky]                                           // 配列以外の場合
            );
        }

        return $array1;
    }



    /**
     * callable無名関数がtrueの場合に配列要素を残す
     *
     * @param callable $callable
     * @return self
     */
    public function filter(callable $callable): self
    {
        $results = [];
        foreach ($this->source as $ky => $vl)
        {
            if (true === $callable($ky, $vl))
            {
                $results[$ky] = $vl;
            }
        }
        $this->source = $results;
        return $this;
    }
}