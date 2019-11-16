<?php

use Citrus\Logger;

require __DIR__ . '/../vendor/autoload.php';

\Citrus\Citrus::initialize();

// ユニットテスト用
define('UNIT_TEST', true);

// 設定ファイル
$configures = require(dirname(__DIR__). '/tests/citrus-configure.php');
// ロガー初期化
Logger::initialize($configures);