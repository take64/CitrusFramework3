<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Accessor;
use PHPUnit\Framework\TestCase;

/**
 * オブジェクトアクセサのテスト
 */
class AccessorTest extends TestCase
{
    /**
     * @test
     */
    public function オブジェクトの追加削除操作が可能()
    {
        $hogehoge = new HogehogeObject();
        // 値設定ができる
        $hogehoge->set('a1', 1);
        // 値取得ができる(設定する)
        $hogehoge->set('a2', $hogehoge->get('a1'));
        $hogehoge->set('a3', $hogehoge->get('a2') + 1);
        // 配列として設定できる
        $hogehoge->add('a4', $hogehoge->get('a3'));
        // 削除できる
        $hogehoge->remove('a1');

        // 検算
        // a1 = null
        // a2 = 1
        // a3 = 1 + 1
        // a4 = [2]
        $this->assertNull($hogehoge->a1);
        $this->assertSame(1, $hogehoge->a2);
        $this->assertSame(2, $hogehoge->a3);
        $this->assertIsArray($hogehoge->a4);
        $this->assertSame(2, $hogehoge->a4[0]);
    }



    /**
     * @test
     */
    public function オブジェクト当て込みが可能()
    {
        $hoge1 = new HogehogeObject();
        $hoge2 = new HogehogeObject();
        // 設定
        $hoge1->a1 = 1;
        $hoge1->a2 = 2;
        $hoge1->a3 = 3;
        $hoge1->a4 = 4;

        // 設定
        $hoge2->bindObject($hoge1);

        // 検算
        $this->assertSame(1, $hoge2->a1);
        $this->assertSame(2, $hoge2->a2);
        $this->assertSame(3, $hoge2->a3);
        $this->assertSame(4, $hoge2->a4);
    }

}

class HogehogeObject extends Accessor
{
    public $a1;
    public $a2;
    public $a3;
    public $a4;
}
