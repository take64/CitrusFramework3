<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Configure;

use Citrus\Collection;

/**
 * 設定値制御下用の抽象クラス
 */
abstract class Configurable
{
    /**
     * 設定値保持
     *
     * @var array [['設定キー' => '設定値', ...]]
     */
    public $configures = [];



    /**
     * 設定値配列の読み込み
     *
     * @param array $configures 設定値配列(全て)
     * @return self
     * @throws ConfigureException
     */
    public function loadConfigures(array $configures = []): self
    {
        // ドメイン設定されている
        $is_domainable = $this->isDomainable($configures);

        $defaults = []; // デフォルト設定
        $domains = [];  // ドメイン設定

        // ドメイン設定されていない
        if (false === $is_domainable)
        {
            $defaults = ($configures[$this->configureKey()] ?? []);
        }
        // ドメイン設定されている
        else
        {
            $defaults = ($configures['default'][$this->configureKey()] ?? []);
            $domains = ($configures[$this->domainCode()][$this->configureKey()] ?? []);
        }

        // デフォルトに、ドメイン設定、デフォルト設定をマージする
        $this->configures = Collection::stream($defaults)
            ->betterMerge($domains)
            ->betterMerge($this->configureDefaults())
            ->toList();

        // 設定値チェック
        $this->validation();

        return $this;
    }



    /**
     * 設定ルートキー
     *
     * @return string
     */
    abstract protected function configureKey(): string;



    /**
     * デフォルト設定値
     *
     * @return array [['設定キー' => '設定値', ...]]
     */
    abstract protected function configureDefaults(): array;



    /**
     * 必須設定値
     *
     * @return string[]
     */
    abstract protected function configureRequires(): array;



    /**
     * ドメイン設定されている設定配列かどうか
     *
     * @param array $configures 設定値配列(全て)
     * @return bool true:ドメイン設定されている,false:されていない
     */
    private function isDomainable(array $configures = []): bool
    {
        // 設定値配列に 'default' が有り、同階層の配列数が2以上の場合がドメイン設定されている
        return (true === array_key_exists('default', $configures) and 2 <= count($configures));
    }



    /**
     * ドメインコードの取得
     *
     * @return string ドメインコードの取得
     * @throws ConfigureException
     */
    private function domainCode(): string
    {
        // httpアクセスの場合
        if (true === isset($_SERVER['HTTP_HOST']))
        {
            return $_SERVER['HTTP_HOST'];
        }

        // コマンドラインアクセスの場合
        $options = getopt('', [
            'domain::',
        ]);
        if (true === isset($options['domain']))
        {
            return $options['domain'];
        }

        // デバッグ
        if (true === defined('UNIT_TEST') and true === UNIT_TEST)
        {
            return 'example.com';
        }

        throw new ConfigureException('設定ファイルのドメイン設定が不明です');
    }


    /**
     * 必須チェック
     *
     * @return void
     * @throws ConfigureException
     */
    private function validation(): void
    {
        // 必須設定キー
        $require_keys = $this->configureRequires();
        // 設定値
        $configures = $this->configures;
        // チェック
        foreach ($require_keys as $key)
        {
            if (false === array_key_exists($key, $configures))
            {
                throw new ConfigureException(sprintf('設定ファイルに %s の設定が存在しません', $key));
            }
        }
    }
}
