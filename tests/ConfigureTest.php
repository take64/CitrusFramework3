<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\CitrusException;
use Citrus\Configure;
use PHPUnit\Framework\TestCase;

/**
 * 設定ファイル処理のテスト
 */
class ConfigureTest extends TestCase
{
    /**
     * @test
     */
    public function ドメインオーバーライドが使える()
    {
        // db想定
        $configure = [
            'default' => [
                'database' => [
                    'type'      => 'postgres',
                    'hostname'  => 'localhost',
                    'port'      => '5432',
                    'database'  => 'cf_database',
                    'schema'    => 'public',
                    'username'  => 'cf_user_id',
                    'password'  => 'cf_password',
                ],
            ],
            'example.com' => [
                'database' => [
                    'type'      => 'mysql',
                    'port'      => '3306',
                ],
            ],
        ];

        // ドメインでオーバーライドした設定配列を取得
        $result = Configure::call($configure, 'database', 'example.com');

        // 検算
        $this->assertSame($configure['example.com']['database']['type'], $result['type']);
        $this->assertSame($configure['default']['database']['hostname'], $result['hostname']);
        $this->assertSame($configure['example.com']['database']['port'], $result['port']);
        $this->assertSame($configure['default']['database']['database'], $result['database']);
        $this->assertSame($configure['default']['database']['schema'], $result['schema']);
        $this->assertSame($configure['default']['database']['username'], $result['username']);
        $this->assertSame($configure['default']['database']['password'], $result['password']);
    }



    /**
     * @test
     */
    public function 設定キーの必須チェック_足りない場合は例外()
    {
        // 設定
        $configure = [
            'database' => [],
            'mode' => '0666',
        ];

        try
        {
            Configure::requireCheck($configure, [
                'database',
                'mode',
                'owner',
            ]);
        }
        catch (CitrusException $e)
        {
            $this->assertSame('設定ファイルに owner の設定が存在しません', $e->getMessage());
        }
    }
}
