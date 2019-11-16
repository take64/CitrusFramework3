<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Configure\ConfigureException;
use Citrus\Logger;
use Citrus\Logger\LogType;
use PHPUnit\Framework\TestCase;

/**
 * ロガー処理のテスト
 */
class LoggerTest extends TestCase
{
    use TestFile;

    /** @var Logger */
    public $logger;



    /**
     * {@inheritDoc}
     * @throws ConfigureException
     */
    public function setUp(): void
    {
        parent::setUp();

        // 設定ファイル
        $configures = require(dirname(__DIR__). '/tests/citrus-configure.php');
        // 初期化
        Logger::initialize($configures);
        $this->logger = Logger::getInstance();
    }



    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        if (Logger::LOG_TYPE_FILE === $this->logger->configures['type'])
        {
            // ディレクトリがあったら削除
            $this->forceRemove($this->logger->configures['directory']);
        }
    }



    /**
     * @test
     */
    public function ファイル出力できる()
    {
        // 出力
        Logger::info('testtest');
    }
}
