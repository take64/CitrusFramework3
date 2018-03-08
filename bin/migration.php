#!/usr/bin/env php
<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */
/**
 * sample :
 *
./bin/migration --directory=../medica6.project.tk --action=decoy
./bin/migration --directory=../medica6.project.tk --action=generate --name=CreateTableActors
./bin/migration --directory=../medica6.project.tk --action=up
./bin/migration --directory=../medica6.project.tk --action=up --version=20170717031426
 */
namespace Citrus\Bin;

date_default_timezone_set('Asia/Tokyo');

include_once dirname(__FILE__) . '/../Configure.class.php';

use Citrus\CitrusConfigure;
use Citrus\CitrusMigration;
use Citrus\CitrusNVL;

// 実行ファイル名削除
unset($argv[0]);

// 設定パース
$settings = [];
foreach ($argv as $arg)
{
    list($ky, $vl) = explode('=', $arg);
    $settings[$ky] = $vl;
}

// 設定(ディレクトリ)
$directory = $settings['--directory'];
// 設定(操作)
$action = $settings['--action'];


// application configure
$application_directory = dirname(__FILE__) . '/../' . $directory;
if (substr($directory, 0, 1) === '/')
{
    $application_directory = $directory;
}
CitrusConfigure::initialize($application_directory . '/citrus-configure.php');

$dsns = [];
foreach (CitrusConfigure::$CONFIGURE_ITEMS as $one)
{
    $key = $one->database->serialize();
    $dsns[$key] = $one->database;
}


// 実行
switch ($action)
{
    // 生成処理
    case CitrusMigration::ACTION_GENERATE :
        $generate_name = $settings['--name'];
        CitrusMigration::generate($application_directory, $generate_name);
        break;
    // マイグレーションUP実行
    case CitrusMigration::ACTION_MIGRATION :
    case CitrusMigration::ACTION_MIGRATION_UP :
        $version = CitrusNVL::ArrayVL($settings, '--version', null);
        $version = CitrusNVL::coalesceNull($version, null);
        CitrusMigration::up($application_directory, $dsns, $version);
        break;
    // マイグレーションDOWN実行
    case CitrusMigration::ACTION_MIGRATION_DOWN :
        $version = CitrusNVL::ArrayVL($settings, '--version', null);
        $version = CitrusNVL::coalesceNull($version, null);
        CitrusMigration::down($application_directory, $dsns, $version);
        break;
    // マイグレーションREBIRTH実行
    case CitrusMigration::ACTION_MIGRATION_REBIRTH :
        $version = CitrusNVL::ArrayVL($settings, '--version', null);
        $version = CitrusNVL::coalesceNull($version, null);
        CitrusMigration::rebirth($application_directory, $dsns, $version);
        break;
    default:
}




