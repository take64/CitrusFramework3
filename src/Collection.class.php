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
    /**
     * 両方の要素を残したいい感じの配列マージ
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function betterMerge(array $array1, array $array2): array
    {
        foreach ($array2 as $ky => $vl)
        {
            $array1[$ky] = (
                true === is_array($vl)
                    ? self::betterMerge($array1[$ky], $array2[$ky]) // 配列の場合
                    : $array2[$ky]                                  // 配列以外の場合
            );
        }

        return $array1;
    }



    /**
     * Closureがtrueの場合に配列要素を残す
     *
     * @param array    $values
     * @param \Closure $closure
     * @return array
     */
    public static function filter(array $values, \Closure $closure): array
    {
        $results = [];
        foreach ($values as $ky => $vl)
        {
            if (true === $closure($ky, $vl))
            {
                $results[$ky] = $vl;
            }
        }
        return $results;
    }
}
