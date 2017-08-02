<?php
/**
 * Generate.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Database
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Database;


use Citrus\Citrus;
use Citrus\CitrusConfigure;
use PDO;

class CitrusDatabaseGenerate
{
    /** @var string */
    const PROPERTY = 'property';

    /** @var string */
    const DAO = 'dao';

    /** @var string */
    const CONDITION = 'condition';



    /**
     * Property
     *
     * @param string              $directory アプリディレクトリ名
     * @param CitrusDatabaseDSN[] $dsns      マイグレーション対象DSNリスト
     * @param string              $tablename テーブル名
     * @param string              $classname class名
     */
    public static function property(string $directory, array $dsns, string $tablename, string $classname)
    {
        // propertyディレクトリパス
        $directory_path = self::callIntegrationPropertyDirectoryPath($directory);

        // DSNの数だけ行う
        foreach ($dsns as $dsn)
        {
            $db = new PDO($dsn->toStringWithAuth());

            // カラム定義の取得
            $stmt = $db->prepare('SELECT 
      column_name
    , column_default
    , data_type
FROM information_schema.columns 
WHERE table_catalog = :database
  AND table_schema = :schema 
  AND table_name = :table 
ORDER BY ordinal_position');
            $stmt->execute([
                ':database' => $dsn->database,
                ':schema'   => $dsn->schema,
                ':table'    => $tablename,
            ]);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // コメント定義の取得
            $stmt = $db->prepare('SELECT
      pg_stat_all_tables.relname
    , pg_attribute.attname
    , pg_description.description
FROM pg_stat_all_tables
INNER JOIN pg_description
        ON pg_description.objoid = pg_stat_all_tables.relid
       AND pg_description.objsubid <> 0
INNER JOIN pg_attribute
        ON pg_attribute.attrelid = pg_description.objoid
       AND pg_attribute.attnum = pg_description.objsubid
WHERE pg_stat_all_tables.schemaname = :schema
  AND pg_stat_all_tables.relname = :table
ORDER BY pg_description.objsubid');
            $stmt->execute([
                ':schema'   => $dsn->schema,
                ':table'    => $tablename,
            ]);
            $comment_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // コメントデータ
            $comments = [];
            foreach ($comment_results as $one)
            {
                $comments[$one['attname']] = $one;
            }

            // デフォルトカラム
            $default_columns = array_keys(get_class_vars(CitrusDatabaseColumn::class));

            // propertyファイル内容
            $file_string = <<<EOT
<?php
/**
 * generated Citrus Property file at {#date#}
 */
 
namespace {#namespace#}\Integration\Property;

use Citrus\Database\CitrusDatabaseColumn;

class {#namespace#}{#class_name#}Property extends CitrusDatabaseColumn
{
{#property#}
}

EOT;
            $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
            $file_string = str_replace('{#namespace#}', ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id), $file_string);
            $file_string = str_replace('{#class_name#}', $classname, $file_string);

            // column property
            $properties = [];
            foreach ($columns as $column)
            {
                // データ取得
                $data_type = $column['data_type'];
                $column_name = $column['column_name'];
                $comment = '';
                if (isset($comments[$column_name]) === true)
                {
                    $comment = $comments[$column_name]['description'];
                }
                $property_name = '$' . $column['column_name'];
                $property_value = $column['column_default'];

                // デフォルトカラムはスルー
                if (in_array($column_name, $default_columns) === true)
                {
                    continue;
                }

                // クラス名置換
                switch ($data_type)
                {
                    case 'character varying' :  $data_type = 'string'; break;
                    case 'text' :               $data_type = 'string'; break;
                    case 'date' :               $data_type = 'string'; break;
                    case 'numeric' :            $data_type = 'int'; break;
                }

                // デフォルト値置換
                if (is_null($property_value) === true)
                {
                    $property_value = 'null';
                }

                // ベース文字列
                $property = <<<PTY
    /** @var {#class_name#} {#comment#} */
    public {#property_name#} = {#property_value#};

PTY;
                // 置換
                $property = str_replace('{#class_name#}',       $data_type,    $property);
                $property = str_replace('{#comment#}',          $comment,       $property);
                $property = str_replace('{#property_name#}',    $property_name, $property);
                $property = str_replace('{#property_value#}',   $property_value,$property);
                $properties[] = $property;
            }

            $file_string = str_replace('{#property#}', implode(PHP_EOL, $properties), $file_string);

            $generate_class_path = sprintf('%s%sProperty.class.php', $directory_path, $classname);
            file_put_contents($generate_class_path, $file_string);
            echo 'generate class file => ' . $generate_class_path . PHP_EOL;
        }
    }



    /**
     * Dao
     *
     * @param string$directory アプリディレクトリ名
     * @param string $tablename テーブル名
     * @param string $classname class名
     */
    public static function dao(string $directory, string $tablename, string $classname)
    {
        // daoディレクトリパス
        $directory_path = self::callIntegrationDaoDirectoryPath($directory);

        // ファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Dao file at {#date#}
 */
 
namespace {#namespace#}\Integration\Dao;

use Citrus\Sqlmap\CitrusSqlmapCrud;

class {#namespace#}{#class_name#}Dao extends CitrusSqlmapCrud
{
    /** @var string sqlmap_id */
    protected \$sqlmap_id = '{#sqlmap_id#}';
    
    /** @var string target */
    protected \$target = '{#tablename#}';
}

EOT;
        $sqlmap_id = implode('', array_map(function($key) {
            return ucfirst($key);
        }, explode('_', $tablename)));

        $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
        $file_string = str_replace('{#namespace#}', ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id), $file_string);
        $file_string = str_replace('{#class_name#}', $classname, $file_string);
        $file_string = str_replace('{#sqlmap_id#}', $sqlmap_id, $file_string);
        $file_string = str_replace('{#tablename#}', $tablename, $file_string);

        $generate_class_path = sprintf('%s%sDao.class.php', $directory_path, $classname);
        file_put_contents($generate_class_path, $file_string);
        echo 'generate class file => ' . $generate_class_path . PHP_EOL;
    }



    /**
     * Condition
     *
     * @param string $directory     アプリディレクトリ名
     * @param string $tablename     テーブル名
     * @param string $conditionname class名
     */
    public static function condition(string $directory, string $tablename, string $conditionname)
    {
        // propertyディレクトリパス
        $directory_path = self::callIntegrationConditionDirectoryPath($directory);

        // propertyファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Condition file at {#date#}
 */
 
namespace {#namespace#}\Integration\Condition;

use {#namespace#}\Integration\Property\{#namespace#}{#table_name#}Property;
use Citrus\Sqlmap\CitrusSqlmapCondition;

class {#namespace#}{#class_name#}Condition extends {#namespace#}{#table_name#}Property
{
    use CitrusSqlmapCondition;
}

EOT;
        $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
        $file_string = str_replace('{#namespace#}', ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id), $file_string);
        $file_string = str_replace('{#table_name#}', $tablename, $file_string);
        $file_string = str_replace('{#class_name#}', $conditionname, $file_string);

        $generate_class_path = sprintf('%s%sCondition.class.php', $directory_path, $conditionname);
        file_put_contents($generate_class_path, $file_string);
        echo 'generate class file => ' . $generate_class_path . PHP_EOL;
    }



    /**
     * Propertyファイル格納ディレクトリパスの取得
     *
     * @param string $directory
     * @return string
     */
    private static function callIntegrationPropertyDirectoryPath(string $directory) : string
    {
        $path = sprintf('%s/Integration/Property/', $directory);

        // ディレクトリがなければ生成
        if (file_exists($path) === false)
        {
            mkdir($path, 0666, true);
            chown($path, 'wwwrun');
            chgrp($path, 'www');
        }

        return $path;
    }



    /**
     * Daoファイル格納ディレクトリパスの取得
     *
     * @param string $directory
     * @return string
     */
    private static function callIntegrationDaoDirectoryPath(string $directory) : string
    {
        $path = sprintf('%s/Integration/Dao/', $directory);

        // ディレクトリがなければ生成
        if (file_exists($path) === false)
        {
            mkdir($path, 0666, true);
            chown($path, 'wwwrun');
            chgrp($path, 'www');
        }

        return $path;
    }



    /**
     * Conditionファイル格納ディレクトリパスの取得
     *
     * @param string $directory
     * @return string
     */
    private static function callIntegrationConditionDirectoryPath(string $directory) : string
    {
        $path = sprintf('%s/Integration/Condition/', $directory);

        // ディレクトリがなければ生成
        if (file_exists($path) === false)
        {
            mkdir($path, 0666, true);
            chown($path, 'wwwrun');
            chgrp($path, 'www');
        }

        return $path;
    }
}