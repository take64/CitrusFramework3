<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Configure;

use Citrus\Configure\Configurable;
use PHPUnit\Framework\TestCase;

/**
 * 設定値制御下用の抽象クラスのテスト
 */
class ConfigurableTest extends TestCase
{
    /**
     * @test
     */
    public function test_todo()
    {
        $this->assertTrue(true);
    }
}

/**
 * サンプルクラス
 */
class SampleConfigure extends Configurable
{
    /**
     * 設定ルートキー
     *
     * @return string
     */
    protected function configureKey(): string
    {
        return 'test';
    }



    /**
     * デフォルト設定値
     *
     * @return array [['設定キー' => '設定値', ...]]
     */
    protected function configureDefaults(): array
    {
        return [];
    }



    /**
     * 必須設定値
     *
     * @return string[]
     */
    protected function configureRequires(): array
    {
        return [];
    }
}
