<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Database;

use Citrus\CitrusException;
use Citrus\Configure\ConfigureException;
use Citrus\Database\Connection;
use Citrus\Database\DSN;
use Citrus\Database\Generate;
use Citrus\Migration;
use Citrus\Sqlmap\SqlmapException;
use PHPUnit\Framework\TestCase;
use Test\TestFile;

/**
 * データベース接続のテスト
 */
class ConnectionTest extends TestCase
{
    /** @var array 設定配列 */
    private $configures;



    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // 設定配列
        $this->configures = [
            'default' => [
                'database' => [
                    'type'      => 'sqlite',
                    'hostname'  => ':memory:',
                ],
            ],
        ];
    }



    /**
     * @test
     * @throws ConfigureException
     * @throws SqlmapException
     * @doesNotPerformAssertions
     */
    public function 接続処理ができる()
    {
        // DSN生成
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        // 接続
        $con = new Connection($dsn);
        $con->connect();
    }
}
