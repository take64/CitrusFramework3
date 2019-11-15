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
    public static function goodMerge(array $array1, array $array2): array
    {
        foreach ($array2 as $ky => $vl)
        {
            // 配列である
            if (true === is_array($vl))
            {
                $array1[$ky] = self::goodMerge($array1[$ky], $array2[$ky]);
            }
            // 普通の値
            else
            {
                $array1[$ky] = $array2[$ky];
            }
        }

        return $array1;
    }
}