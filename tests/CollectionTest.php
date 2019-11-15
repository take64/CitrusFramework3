<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Collection;
use PHPUnit\Framework\TestCase;

/**
 * コレクションクラスのテスト
 */
class CollectionTest extends TestCase
{
    /**
     * @test
     */
    public function 両方の要素を残したいい感じの配列マージ()
    {
        $array1 = [
            'a' => 1,
            'b' => 2,
            'c' => [
                'd' => 3,
                'e' => 4,
            ],
            'f' => 5,
        ];
        $array2 = [
            'a' => 5,
            'c' => [
                'g' => 6,
            ],
            'h' => 7,
        ];

        $expected = [
            'a' => 5,
            'b' => 2,
            'c' => [
                'd' => 3,
                'e' => 4,
                'g' => 6,
            ],
            'f' => 5,
            'h' => 7,
        ];

        // いい感じのマージ
        $actual = Collection::goodMerge($array1, $array2);

        // 検算
        $this->assertSame($expected, $actual);
    }
}
