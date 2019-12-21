<?php

require __DIR__ . '/../vendor/autoload.php';

\Citrus\Citrus::initialize();

// ユニットテスト用
define('UNIT_TEST', true);

// 設定ファイル
$configure_path = dirname(__DIR__). '/tests/citrus-configure.php';
\Citrus\Configure::initialize($configure_path);

\Citrus\Session::factory();

// ロガー初期化
$configures = include($configure_path);
\Citrus\Logger::sharedInstance()->loadConfigures($configures);