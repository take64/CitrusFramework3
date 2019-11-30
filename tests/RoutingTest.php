<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Configure\ConfigureException;
use Citrus\Configure\Routing;
use PHPUnit\Framework\TestCase;

/**
 * ルーティング処理のテスト
 */
class RoutingTest extends TestCase
{
    /**
     * @test
     * @throws ConfigureException
     */
    public function 設定を読み込んで適用できる()
    {
        // 設定値
        $configures = [
            'routing' => [
                'default'   => 'home/index',
                'login'     => 'home/login',
                'error404'  => 'page/error404',
                'error503'  => 'page/error503',
            ],
        ];

        // 生成
        /** @var Routing $routing */
        $routing = Routing::getInstance()->loadConfigures($configures);

        // 検証
        $this->assertSame($configures['routing']['default'], $routing->default);
        $this->assertSame($configures['routing']['login'], $routing->login);
        $this->assertSame($configures['routing']['error404'], $routing->error404);
        $this->assertSame($configures['routing']['error503'], $routing->error503);
    }
}
