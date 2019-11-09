<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Command\Console;

use Citrus\Command\Console\Decorate;
use PHPUnit\Framework\TestCase;

/**
 * コマンドコンソール装飾のテスト
 */
class DecorateTest extends TestCase
{
    /**
     * @test
     */
    public function 文字に色をつけて出力()
    {
        // メッセージ
        $message = ' TEST ';

        // 装飾
        $decorate = new Decorate();

        // 文字色(赤)
        $decorate->onTextColor(Decorate::RED);
        $output = $decorate->format($message);
        $this->debugOutput($output);

        // 背景色(シアン)
        $decorate->onBackColor(Decorate::CYAN);
        $output = $decorate->format($message);
        $this->debugOutput($output);

        // 太字
        $decorate->onBold();
        $output = $decorate->format($message);
        $this->debugOutput($output);

        // 下線
        $decorate->onUnderline();
        $output = $decorate->format($message);
        $this->debugOutput($output);

        // 点滅
        $decorate->addStack(Decorate::BLINK);
        $output = $decorate->format($message);
        $this->debugOutput($output);

        // 反転
        $decorate->addStack(Decorate::REVERSE);
        $output = $decorate->format($message);
        $this->debugOutput($output);
    }



    /**
     * 目視用のデバッグプリント
     *
     * @param string $message
     * @return void
     */
    private function debugOutput(string $message): void
    {
//        echo $message . "\n";
    }
}
