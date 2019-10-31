<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Citrus;
use Citrus\CitrusException;
use Citrus\Command\Console;
use Citrus\Configure;
use Citrus\Database\Catalog\CatalogManager;

/**
 * データベースオブジェクト生成処理
 */
class Generate
{
    use Console;

    /** @var string Propertyクラス */
    const TYPE_PROPERTY = 'property';

    /** @var string Daoクラス */
    const TYPE_DAO = 'dao';

    /** @var string Conditionクラス */
    const TYPE_CONDITION = 'condition';

    /** @var string 全て */
    const TYPE_ALL = 'all';

    /** @var array 設定ファイル */
    protected $configure;

    /** @var CatalogManager カタログマネージャ */
    protected $catalogManager;



    /**
     * constructor.
     *
     * @param array|null $citrus_configure Citrus設定ファイル
     * @throws CitrusException
     */
    public function __construct(array $citrus_configure = [])
    {
        if (0 < count($citrus_configure))
        {
            $this->setupConfigure($citrus_configure);
        }
    }



    /**
     * 設定ファイルの設定とチェック
     *
     * @param array $citrus_configure
     * @return void
     * @throws CitrusException
     */
    public function setupConfigure(array $citrus_configure): void
    {
        // 設定値チェック
        Configure::requireCheck($citrus_configure, [
            'database',
            'mode',
            'owner',
            'group',
            'output_dir',
            'namespace',
        ]);
        $this->configure = $citrus_configure;

        // 出力ファイル出力パスの設定
        self::setupOutputDirectory();

        // DSN情報
        $dsn = new DSN();
        $dsn->bind($this->configure['database']);

        // カタログマネージャ
        $this->catalogManager = new CatalogManager($dsn);
    }



    /**
     * Conditionクラスの生成
     *
     * @param string $table_name   テーブル名
     * @param string $class_prefix クラス接頭辞
     */
    public function condition(string $table_name, string $class_prefix)
    {
        // 生成クラス名
        $class_name = $class_prefix;
        // 出力ディレクトリ
        $output_dir = $this->configure['output_dir'];

        // propertyファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Condition file at {#date#}
 */
 
namespace {#namespace#}\Integration\Condition;

use {#namespace#}\Integration\Property\{#namespace#}{#class_name#}Property;
use Citrus\Sqlmap\CitrusSqlmapCondition;

class {#namespace#}{#class_name#}Condition extends {#namespace#}{#class_name#}Property
{
    use CitrusSqlmapCondition;
}

EOT;
        $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
        $file_string = str_replace('{#namespace#}', $this->configure['namespace'], $file_string);
        $file_string = str_replace('{#table_name#}', $table_name, $file_string);
        $file_string = str_replace('{#class_name#}', $class_name, $file_string);

        $generate_class_path = sprintf('%s/Condition/%sCondition.class.php', $output_dir, $class_name);
        file_put_contents($generate_class_path, $file_string);
        $this->success(sprintf('generate class file => %s', $generate_class_path));
    }



    /**
     * Daoクラスの生成
     *
     * @param string $table_name   テーブル名
     * @param string $class_prefix クラス接頭辞
     */
    public function dao(string $table_name, string $class_prefix)
    {
        // 生成クラス名
        $class_name = $class_prefix;
        // 出力ディレクトリ
        $output_dir = $this->configure['output_dir'];

        // ファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Dao file at {#date#}
 */
 
namespace {#namespace#}\Integration\Dao;

use Citrus\Sqlmap\Crud;

class {#namespace#}{#class_name#}Dao extends Crud
{
    /** @var string sqlmap_id */
    protected \$sqlmap_id = '{#sqlmap_id#}';
    
    /** @var string target */
    protected \$target = '{#table_name#}';
}

EOT;
        $sqlmap_id = implode('', array_map(function($key) {
            return ucfirst($key);
        }, explode('_', $table_name)));

        $file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $file_string);
        $file_string = str_replace('{#namespace#}', $this->configure['namespace'], $file_string);
        $file_string = str_replace('{#class_name#}', $class_name, $file_string);
        $file_string = str_replace('{#sqlmap_id#}', $sqlmap_id, $file_string);
        $file_string = str_replace('{#table_name#}', $table_name, $file_string);

        $generate_class_path = sprintf('%s/Dao/%sDao.class.php', $output_dir, $class_name);
        file_put_contents($generate_class_path, $file_string);
        $this->success(sprintf('generate class file => %s', $generate_class_path));
    }



    /**
     * Propertyクラスの生成
     *
     * @param string $table_name   テーブル名
     * @param string $class_prefix クラス接頭辞
     */
    public function property(string $table_name, string $class_prefix): void
    {
        // 生成クラス名
        $class_name = $class_prefix;
        // 出力ディレクトリ
        $output_dir = $this->configure['output_dir'];
        // カラム定義の取得
        $columns = $this->catalogManager->tableColumns($table_name);
        // コメント定義の取得
        $comments = $this->catalogManager->columnComments($table_name);
        // プライマリキーの取得
        $primary_keys = $this->catalogManager->primaryKeys($table_name);
        // デフォルトカラム
        $default_columns = array_keys(get_class_vars(Column::class));

        // propertyファイル内容
        $file_string = <<<EOT
<?php
/**
 * generated Citrus Property file at {#date#}
 */
 
namespace {#namespace#}\Integration\Property;

use Citrus\Database\Column;
use {#namespace#}\Integration\Condition\{#namespace#}{#class_name#}Condition;

class {#namespace#}{#class_name#}Property extends Column
{
{#property#}


    /**
     * call primary keys
     *
     * @return string[]
     */
    public function callPrimaryKeys(): array
    {
        return [{#primary_keys#}];
    }



    /**
     * call condition
     *
     * @return {#namespace#}{#class_name#}Condition
     */
    public function callCondition(): {#namespace#}{#class_name#}Condition
    {
        if (is_null(\$this->condition) === true)
        {
            \$this->condition = new {#namespace#}{#class_name#}Condition();
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
        $file_string = str_replace('{#namespace#}', $this->configure['namespace'], $file_string);
        $file_string = str_replace('{#class_name#}', $class_name, $file_string);
        $file_string = str_replace('{#primary_keys#}', implode(', ', $primary_keys), $file_string);

        // column property
        $properties = [];
        foreach ($columns as $column_name => $columnDef)
        {
            // データ取得
            $data_type = self::convertToPHPType($columnDef->data_type);
            $column_name = $columnDef->column_name;
            $comment = '';
            if (true === array_key_exists($column_name, $comments))
            {
                $comment = $comments[$column_name]->comment;
            }
            $property_name = '$' . $columnDef->column_name;

            // デフォルトカラムはスルー
            if (true === in_array($column_name, $default_columns))
            {
                continue;
            }

            // ベース文字列
            $property = <<<EOT
    /** @var {#class_name#} {#comment#} */
    public {#property_name#};

EOT;
            // 置換
            $property = str_replace('{#class_name#}',   $data_type,     $property);
            $property = str_replace('{#comment#}',      $comment,       $property);
            $property = str_replace('{#property_name#}',$property_name, $property);
            $properties[] = $property;
        }

        $file_string = str_replace('{#property#}', implode(PHP_EOL, $properties), $file_string);

        $generate_class_path = sprintf('%s/Property/%sProperty.class.php', $output_dir, $class_name);
        file_put_contents($generate_class_path, $file_string);
        $this->success(sprintf('generate class file => %s', $generate_class_path));
    }



    /**
     * クラスの一括生成
     *
     * @param string $table_name   テーブル名
     * @param string $class_prefix クラス接頭辞
     */
    public function all(string $table_name, string $class_prefix)
    {
        $this->condition($table_name, $class_prefix);
        $this->dao($table_name, $class_prefix);
        $this->property($table_name, $class_prefix);
    }



    /**
     * テーブルカラムの型からPHPの型に変換
     *
     * @param string $data_type カラムデータタイプ
     * @return string PHPの型
     */
    private static function convertToPHPType(string $data_type): string
    {
        switch ($data_type)
        {
            case 'character varying':
            case 'text':
            case 'date':
            case 'timestamp without time zone':
                // 文字列
                $data_type = 'string';
                break;
            case 'integer':
            case 'bigint':
                // 整数
                $data_type = 'int';
                break;
            case 'numeric':
                // 浮動小数点
                $data_type = 'double';
                break;
            default:
        }
        return $data_type;
    }




    /**
     * 出力ファイル格納ディレクトリパスの設定
     *
     * @return void
     */
    private function setupOutputDirectory(): void
    {
        // 出力ディレクトリ
        $parent_dir = $this->configure['output_dir'];

        // 各ディレクトリ
        $dirs = [
            '/Condition',
            '/Dao',
            '/Property',
        ];

        foreach ($dirs as $dir)
        {
            // 各出力ディレクトリ
            $output_dir = ($parent_dir . $dir);

            // ディレクトリがなければ生成
            if (false === file_exists($output_dir))
            {
                mkdir($output_dir);
                chmod($output_dir, $this->configure['mode']);
                chown($output_dir, $this->configure['owner']);
                chgrp($output_dir, $this->configure['group']);
            }
        }
    }
}