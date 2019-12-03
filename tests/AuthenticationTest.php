<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Authentication;
use Citrus\Authentication\Database;
use Citrus\Configure\ConfigureException;
use PHPUnit\Framework\TestCase;

/**
 * 認証処理のテスト
 */
class AuthenticationTest extends TestCase
{
    /**
     * @test
     * @throws ConfigureException
     */
    public function 設定を読み込んで適用できる()
    {
        // 設定ファイル
        $configures = require(dirname(__DIR__) . '/tests/citrus-configure.php');

        // 生成
        /** @var Authentication $authentication */
        $authentication = Authentication::getInstance()->loadConfigures($configures);

        // 検証
        $this->assertInstanceOf(Database::class, $authentication->protocol);
    }
}
