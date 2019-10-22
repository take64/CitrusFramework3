<?php
/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Command\Console;

/**
 * コンソール出力文字列の装飾
 */
class Decorate
{
    /** @var int 文字色デフォルトカラー接頭辞 */
    const FOREGROUND_DEFAULT_PREFIX = 3;

    /** @var int 文字色ライトカラー接頭辞 */
    const FOREGROUND_LIGHT_PREFIX = 9;

    /** @var int 背景色デフォルトカラー接頭辞 */
    const BACKGROUND_DEFAULT_PREFIX = 4;

    /** @var int 背景色ライトカラー接頭辞 */
    const BACKGROUND_LIGHT_PREFIX = 10;

    /** @var int 黒 */
    const BLACK = 0;

    /** @var int 赤 */
    const RED = 1;

    /** @var int 緑 */
    const GREEN = 2;

    /** @var int 黄色 */
    const YELLOW = 3;

    /** @var int 青 */
    const BLUE = 4;

    /** @var int マジェンタ */
    const MAGENTA = 5;

    /** @var int シアン */
    const CYAN = 6;

    /** @var int 白 */
    const WHITE = 7;

    /** @var int 太字 */
    const BOLD = 1;

    /** @var int 仄暗くする */
    const DIM = 2;

    /** @var int 下線 */
    const UNDERLINE = 4;

    /** @var int 点滅 */
    const BLINK = 5;

    /** @var int 反転 */
    const REVERSE = 7;

    /** @var int 隠す */
    const HIDDEN = 8;

    /** @var string 初期化 */
    const RESET = "\033[m";

    /** @var int[] 装飾のスタック */
    protected $stack = [];



    /**
     * 装飾を積む
     *
     * @param int|int[] $decorates
     * @return void
     */
    public function addStack($decorates): void
    {
        // 配列の場合は再起
        if (true === is_array($decorates))
        {
            foreach ($decorates as $decorate)
            {
                $this->addStack($decorate);
            }
        }

        $this->stack[] = $decorates;
    }



    /**
     * 装飾済み文字列の返却
     *
     * @param string $message
     * @return string
     */
    public function format(string $message): string
    {
        $decorate_code = implode(';', $this->stack);

        return sprintf("\033[%sm%s%s",
            $decorate_code,
            $message,
            self::RESET
        );
    }



    /**
     * 太字にする
     *
     * @return void
     */
    public function onBold(): void
    {
        $this->addStack(self::BOLD);
    }



    /**
     * 下線をつける
     *
     * @return void
     */
    public function onUnderline(): void
    {
        $this->addStack(self::UNDERLINE);
    }



    /**
     * 文字色をつける
     *
     * @param int $color 色
     * @return void
     */
    public function onTextColor(int $color): void
    {
        $this->addStack((self::FOREGROUND_DEFAULT_PREFIX * 10) + $color);
    }



    /**
     * 文字色をつける(淡)
     *
     * @param int $color 色
     * @return void
     */
    public function onTextLightColor(int $color): void
    {
        $this->addStack((self::FOREGROUND_LIGHT_PREFIX * 10) + $color);
    }



    /**
     * 背景色をつける
     *
     * @param int $color 色
     * @return void
     */
    public function onBackColor(int $color): void
    {
        $this->addStack((self::BACKGROUND_DEFAULT_PREFIX * 10) + $color);
    }



    /**
     * 背景色をつける(淡)
     *
     * @param int $color 色
     * @return void
     */
    public function onBackLightColor(int $color): void
    {
        $this->addStack((self::BACKGROUND_LIGHT_PREFIX * 10) + $color);
    }
}