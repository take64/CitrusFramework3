<?php
/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Command;

use Citrus\Command\Console\Decorate;

trait Console
{
    /**
     * メッセージ出力
     *
     * @param string|string[] $messages 出力メッセージ
     * @param bool            $newline  改行
     * @return void
     */
    public function write($messages, bool $newline): void
    {
        // 配列なら再起
        if (true === is_array($messages))
        {
            foreach ($messages as $message)
            {
                $this->write($message, $newline);
            }
        }

        // テスト時は出力をせずにreturn
        if (true === defined('UNIT_TEST') && true === UNIT_TEST)
        {
            return;
        }

        // 出力
        echo $messages . (true === $newline ? PHP_EOL : '');
    }



    /**
     * メッセージ出力(改行)
     *
     * @param string|string[] $messages メッセージ
     * @return void
     */
    public function writeln($messages): void
    {
        $this->write($messages, true);
    }



    /**
     * メッセージフォーマット(改行)
     *
     * @param string $format フォーマット文字列
     * @param null   $args   無限引数用
     * @param null   $_      無限引数用
     * @return void
     */
    public function format(string $format, $args = null, $_ = null): void
    {
        // 先頭はフォーマットなので削除
        $args = func_get_args();
        unset($args[0]);

        $this->writeln(vsprintf($format, $args));
    }



    /**
     * 成功時出力
     *
     * @param string
     * @return void
     */
    public function success(string $message): void
    {
        // 装飾
        $decorate = new Decorate();
        $decorate->onTextLightColor(Decorate::GREEN);
        $decorate->onBold();
        $decorated_message = $decorate->format($message);
        // 出力
        $this->writeln($decorated_message);
    }



    /**
     * 失敗時出力
     *
     * @param string
     * @return void
     */
    public function failure(string $message): void
    {
        // 装飾
        $decorate = new Decorate();
        $decorate->onTextLightColor(Decorate::WHITE);
        $decorate->onBackColor(Decorate::RED);
        $decorate->onBold();
        $decorated_message = $decorate->format($message);
        // 出力
        $this->writeln($decorated_message);
    }
}