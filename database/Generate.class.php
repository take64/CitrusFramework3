<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
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
     * @param CitrusDatabaseDSN[] $dsns      マイグレーション対象DSNリスト
     * @param string              $tablename テーブル名
     * @param string              $classname class名
     */
    public static function property(array $dsns, string $tablename, string $classname)
    {
        // propertyディレクトリパス
        $directory_path = self::callDirectoryPath(CitrusConfigure::$DIR_INTEGRATION_PROPERTY);

        // DSNの数だけ行う
        foreach ($dsns as $dsn)
        {
            $db = new PDO($dsn->toStringWithAuth());

            // カラム定義の取得
            $columns = self::callTableColumns($db, $dsn, $tablename);

            // コメント定義の取得
            $comments = self::callTableColumnComments($db, $dsn, $tablename);


            // プライマリキーの取得
            $primary_keys = self::callTablePrimaryKeys($db, $tablename);

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
use {#namespace#}\Integration\Condition\{#namespace#}{#class-name#}Condition;

class {#namespace#}{#class-name#}Property extends CitrusDatabaseColumn
{
{#property#}


    /**
     * call primary keys
     *
     * @return string[]
     */
    public function callPrimaryKeys() : array
    {
        return [{#primary-keys#}];
    }



    /**
     * call condition
     *
     * @return {#namespace#}{#class-name#}Condition
     */
    public function callCondition() : {#namespace#}{#class-name#}Condition
    {
        if (is_null(\$this->condition) === true)
        {
            \$this->condition = new {#namespace#}{#class-name#}Condition();
            \$this->condition->nullify();
        }
        \$primary_keys = \$this->callPrimaryKeys();
        foreach (\$primary_keys as \$primary_key)
        {
            \$this->condition->\$primary_key = \$this->\$primary_key;
        }

        return \$this->condition;
    }
}

EOT;
            $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
            $file_string = str_replace('{#namespace#}', ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id), $file_string);
            $file_string = str_replace('{#class-name#}', $classname, $file_string);
            $file_string = str_replace('{#primary-keys#}', implode(', ', $primary_keys), $file_string);

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

                // デフォルトカラムはスルー
                if (in_array($column_name, $default_columns) === true)
                {
                    continue;
                }

                // クラス名置換
                switch ($data_type)
                {
                    case 'character varying' :
                    case 'text' :
                    case 'date' :
                    case 'timestamp without time zone' :
                        $data_type = 'string';
                        break;
                    case 'numeric' :
                        $data_type = 'int';
                        break;
                    default:
                }

                // ベース文字列
                $property = <<<PTY
    /** @var {#class-name#} {#comment#} */
    public {#property_name#};

PTY;
                // 置換
                $property = str_replace('{#class-name#}',   $data_type,     $property);
                $property = str_replace('{#comment#}',      $comment,       $property);
                $property = str_replace('{#property_name#}',$property_name, $property);
                $properties[] = $property;
            }

            $file_string = str_replace('{#property#}', implode(PHP_EOL, $properties), $file_string);

            $generate_class_path = sprintf('%s/%sProperty.class.php', $directory_path, $classname);
            file_put_contents($generate_class_path, $file_string);
            echo 'generate class file => ' . $generate_class_path . PHP_EOL;
        }
    }



    /**
     * Dao
     *
     * @param string $tablename テーブル名
     * @param string $classname class名
     */
    public static function dao(string $tablename, string $classname)
    {
        // daoディレクトリパス
        $directory_path = self::callDirectoryPath(CitrusConfigure::$DIR_INTEGRATION_DAO);

        // ファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Dao file at {#date#}
 */
 
namespace {#namespace#}\Integration\Dao;

use Citrus\Sqlmap\CitrusSqlmapCrud;

class {#namespace#}{#class-name#}Dao extends CitrusSqlmapCrud
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
        $file_string = str_replace('{#class-name#}', $classname, $file_string);
        $file_string = str_replace('{#sqlmap_id#}', $sqlmap_id, $file_string);
        $file_string = str_replace('{#tablename#}', $tablename, $file_string);

        $generate_class_path = sprintf('%s/%sDao.class.php', $directory_path, $classname);
        file_put_contents($generate_class_path, $file_string);
        echo 'generate class file => ' . $generate_class_path . PHP_EOL;
    }



    /**
     * Condition
     *
     * @param string $tablename     テーブル名
     * @param string $conditionname class名
     */
    public static function condition(string $tablename, string $conditionname)
    {
        // propertyディレクトリパス
        $directory_path = self::callDirectoryPath(CitrusConfigure::$DIR_INTEGRATION_CONDITION);

        // propertyファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Condition file at {#date#}
 */
 
namespace {#namespace#}\Integration\Condition;

use {#namespace#}\Integration\Property\{#namespace#}{#table_name#}Property;
use Citrus\Sqlmap\CitrusSqlmapCondition;

class {#namespace#}{#class-name#}Condition extends {#namespace#}{#table_name#}Property
{
    use CitrusSqlmapCondition;
}

EOT;
        $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
        $file_string = str_replace('{#namespace#}', ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id), $file_string);
        $file_string = str_replace('{#table_name#}', $tablename, $file_string);
        $file_string = str_replace('{#class-name#}', $conditionname, $file_string);

        $generate_class_path = sprintf('%s/%sCondition.class.php', $directory_path, $conditionname);
        file_put_contents($generate_class_path, $file_string);
        echo 'generate class file => ' . $generate_class_path . PHP_EOL;
    }



    /**
     * ファイル格納ディレクトリパスの取得
     *
     * @param string $path
     * @return string
     */
    private static function callDirectoryPath(string $path) : string
    {
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
     * テーブルのカラム定義の取得
     *
     * @param PDO               $db        PDOハンドル
     * @param CitrusDatabaseDSN $dsn       DSN設定
     * @param string            $tablename テーブル名
     * @return array
     */
    private static function callTableColumns(PDO $db, CitrusDatabaseDSN $dsn, string $tablename)
    {
        $stmt = $db->prepare(<<<SQL
SELECT 
      column_name
    , column_default
    , data_type
FROM information_schema.columns 
WHERE table_catalog = :database
  AND table_schema = :schema 
  AND table_name = :table 
ORDER BY ordinal_position
SQL
);
        $stmt->execute([
            ':database' => $dsn->database,
            ':schema'   => $dsn->schema,
            ':table'    => $tablename,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * テーブルのカラム定義の取得
     *
     * @param PDO               $db        PDOハンドル
     * @param CitrusDatabaseDSN $dsn       DSN設定
     * @param string            $tablename テーブル名
     * @return array
     */
    private static function callTableColumnComments(PDO $db, CitrusDatabaseDSN $dsn, string $tablename)
    {
        $stmt = $db->prepare(<<<SQL
SELECT
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
ORDER BY pg_description.objsubid
SQL
);
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

        return $comments;
    }



    /**
     * テーブルのプライマリキー定義の取得
     *
     * @param PDO               $db        PDOハンドル
     * @param string            $tablename テーブル名
     * @return array
     */
    private static function callTablePrimaryKeys(PDO $db, string $tablename)
    {
        $stmt = $db->prepare(<<<SQL
SELECT
      information_schema.constraint_column_usage.column_name
FROM information_schema.table_constraints
INNER JOIN information_schema.constraint_column_usage
        ON information_schema.constraint_column_usage.constraint_name = information_schema.table_constraints.constraint_name
       AND information_schema.constraint_column_usage.table_name = information_schema.table_constraints.table_name
       AND information_schema.constraint_column_usage.table_schema = information_schema.table_constraints.table_schema
       AND information_schema.constraint_column_usage.table_catalog = information_schema.table_constraints.table_catalog
       AND information_schema.constraint_column_usage.table_catalog = information_schema.table_constraints.table_catalog
WHERE information_schema.table_constraints.constraint_type = 'PRIMARY KEY'
  AND information_schema.table_constraints.table_name = :table
SQL
);
        $stmt->execute([
            ':table'    => $tablename,
        ]);
        $primarykey_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // プライマリキーデータ
        $primary_keys = [];
        foreach ($primarykey_results as $one)
        {
            $column_name = $one['column_name'];
            $primary_keys[$column_name] = sprintf('\'%s\'', $column_name);
        }

        return $primary_keys;
    }
}