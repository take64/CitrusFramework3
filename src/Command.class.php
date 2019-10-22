<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

/**
 * コマンド処理
 */
class Command extends Struct
{
    /** @var string script code */
    public $script = '';

    /** @var array configure */
    public $configure = [];

    /** @var array command options */
    protected $options = [];

    /** @var array command parameters */
    protected $parameters = [];



    /**
     * コマンドライン引数のパース処理
     */
    public function options()
    {
        // コマンドライン引数がなければ処理スキップ
        if (0 === count($this->options))
        {
            return;
        }

        $this->parameters = getopt('', $this->options);
    }



    /**
     * コマンド実行処理
     */
    public function execute()
    {

    }



    /**
     * コマンド実行前処理
     */
    public function before()
    {

    }



    /**
     * コマンド実行後処理
     */
    public function after()
    {

    }



    /**
     * コマンドラインオプションパラメータ取得
     *
     * @param string      $key     パラメータキー
     * @param string|null $default デフォルト値
     * @return string パラメータ値
     */
    public function parameter(string $key, string $default = null): string
    {
        return ($this->parameters[$key] ?? $default);
    }



    /**
     * コマンドランナー
     *
     * @param array $configure 設定情報
     * @return void
     */
    public static function runner(array $configure): void
    {
        $command = new static();
        $command->configure = $configure;
        $command->options();
        $command->before();
        $command->execute();
        $command->after();
    }
}