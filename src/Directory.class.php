<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

/**
 * ディレクトリ処理のクラス
 */
class Directory
{
    /**
     * 引数のディレクトリ文字列を適切な形に修飾する
     *
     * @param string $path
     * @return string
     */
    public static function suitablePath(string $path): string
    {
        // パスを分解
        $paths = explode('/', $path);
        // 逆順
        $paths = array_reverse($paths);

        // 相殺レベル
        $offset_level = 0;
        // 走査
        foreach ($paths as $ky => $vl)
        {
            // 動かないパスは消す
            if ('.' === $vl)
            {
                unset($paths[$ky]);
                continue;
            }

            // 親指定がある場合は次にパスを削除する
            if ('..' === $vl)
            {
                unset($paths[$ky]);
                $offset_level++;
                continue;
            }

            // 相殺レベルがある場合は削除
            if (0 < $offset_level)
            {
                unset($paths[$ky]);
                $offset_level--;
                continue;
            }
        }

        // 正順に戻す
        $paths = array_reverse($paths);

        return implode('/', $paths);
    }
}