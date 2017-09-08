#!/usr/bin/env php
<?php
/**
 * database
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  bin
 * @license     http://www.citrus.tk/
 */
/**
 * sample :
 *
./bin/migration.sh --directory=../medica6.project.tk --action=decoy
./bin/migration.sh --directory=../medica6.project.tk --action=generate --name=CreateTableActors
./bin/migration.sh --directory=../medica6.project.tk --action=up
./bin/migration.sh --directory=../medica6.project.tk --action=up --version=20170717031426
 */
namespace Citrus\Bin;

date_default_timezone_set('Asia/Tokyo');

include_once dirname(__FILE__) . '/../Configure.class.php';

use Citrus\CitrusConfigure;
use Citrus\Database\CitrusDatabaseGenerate;

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
// 設定(タイプ)
$type = $settings['--type'];
// 設定(テーブル名)
$tablename = $settings['--table-name'];

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
switch ($type)
{
    // Entity生成処理
    case CitrusDatabaseGenerate::PROPERTY :
        $propertyname = $settings['--property-name'];
        CitrusDatabaseGenerate::property($dsns, $tablename, $propertyname);
        break;
    // Dao生成処理
    case CitrusDatabaseGenerate::DAO :
        $daoname = $settings['--dao-name'];
        CitrusDatabaseGenerate::dao($tablename, $daoname);
        break;
    // Condition生成処理
    case CitrusDatabaseGenerate::CONDITION :
        $conditionname = $settings['--condition-name'];
        CitrusDatabaseGenerate::condition($tablename, $conditionname);
        break;
}




