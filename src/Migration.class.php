<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Command\Console;
use Citrus\Database\DSN;
use Citrus\Migration\Item;

/**
 * マイグレーション処理
 */
class Migration
{
    /** @var string 生成 */
    const ACTION_GENERATE = 'generate';

    /** @var string マイグレーションUP */
    const ACTION_MIGRATION_UP = 'up';

    /** @var string マイグレーションDOWN */
    const ACTION_MIGRATION_DOWN = 'down';

    /** @var string マイグレーションREBIRTH */
    const ACTION_MIGRATION_REBIRTH = 'rebirth';

    /** @var array 設定ファイル */
    protected $configure;

    /** @var DSN DSN情報 */
    protected $dsn;

    /** @var Console コンソール */
    protected $console;



    /**
     * constructor.
     *
     * @param array|null   $citrus_configure Citrus設定ファイル
     * @param Console|null $console          コンソール
     * @throws CitrusException
     */
    public function __construct(array $citrus_configure = [], Console $console = null)
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
        ]);

        $this->configure = $citrus_configure;

        // DSN情報
        $this->dsn = new DSN();
        $this->dsn->bind($this->configure['database']);
    }



    /**
     * マイグレーションファイル生成
     *
     * @param string $generate_name 生成ファイル名
     * @return void
     */
    public function generate(string $generate_name): void
    {
        // 生成時間
        $timestamp = Citrus::$TIMESTAMP_CHAR14;

        // マイグレーションファイル出力パスの設定
        self::setupOutputDirectory();

        // 対象テーブル名
        $object_name = $generate_name;
        $object_name = str_replace(['CreateTable', 'DropTable', 'AlterTable', 'CreateView', 'DropView' ], '', $object_name);
        $object_name = strtolower($object_name);

        // マイグレーション内容
        $file = <<<EOT
<?php

/**
 * generated Citrus Migration file at {#timestamp#}
 */

use Citrus\Migration\Item;

class {#class_name#} extends Item
{
    public \$object_name = '{#object_name#}';

    public function up()
    {
        \$this->execute(<<<SQL

SQL
        );
    }

    public function down()
    {
        \$this->execute(<<<SQL

SQL
        );
    }
}

EOT;
        $file = str_replace('{#timestamp#}', Citrus::$TIMESTAMP_FORMAT, $file);
        $file = str_replace('{#object_name#}', $object_name, $file);

        // ファイル名
        $class_name = sprintf('Citrus_%s_%s', $timestamp, $generate_name);
        $file = str_replace('{#class_name#}', $class_name, $file);

        // generate
        self::saveMigrationFile($class_name, $file);
    }



    /**
     * マイグレーションの正方向実行
     *
     * @param string|null $version バージョン指定(指定がなければ全部)
     * @return void
     */
    public function up(string $version = null): void
    {
        // マイグレーションファイル出力パスの設定
        self::setupOutputDirectory();
        // 出力パス
        $output_dir = $this->configure['output_dir'];

        // 対象ファイルの取得
        $migration_files = scandir($output_dir);

        // 対象の場合は実行
        /** @var string $one ex. Citrus_XXXXXXXXXXXXXX_CreateTableXXXXXs.class.php */
        foreach ($migration_files as $one)
        {
            /** @var Item $instance */
            $instance = $this->callInstance($output_dir, $one, $version);

            // 実行
            $instance->up();
        }
    }



    /**
     * マイグレーション逆方向実行
     *
     * @param string|null $version バージョン指定(指定がなければ全部)
     * @return void
     */
    public function down(string $version = null): void
    {
        // マイグレーションファイル出力パスの設定
        self::setupOutputDirectory();
        // 出力パス
        $output_dir = $this->configure['output_dir'];

        // 対象ファイルの取得
        $migration_files = scandir($output_dir);
        $migration_files = array_reverse($migration_files);

        // 対象の場合は実行
        /** @var string $one ex. Citrus_XXXXXXXXXXXXXX_CreateTableXXXXXs.class.php */
        foreach ($migration_files as $one)
        {
            /** @var Item $instance */
            $instance = $this->callInstance($output_dir, $one, $version);

            // 実行
            $instance->down();
        }
    }



    /**
     * マイグレーションREBIRTHの実行
     *
     * @param string|null $version バージョン指定(指定がなければ全部)
     */
    public function rebirth(string $version = null)
    {
        // DOWN
        self::down($version);
        // UP
        self::up($version);
    }



    /**
     * マイグレーションファイル格納ディレクトリパスの設定
     *
     * @return void
     */
    private function setupOutputDirectory(): void
    {
        // 出力ディレクトリ
        $output_dir = $this->configure['output_dir'];

        // ディレクトリがなければ生成
        if (file_exists($output_dir) === false)
        {
            mkdir($output_dir);
            chmod($output_dir, $this->configure['mode']);
            chown($output_dir, $this->configure['owner']);
            chgrp($output_dir, $this->configure['group']);
        }
    }



    /**
     * 生成したマイグレーションファイルの保存
     *
     * @param string $class_name    生成マイグレーションクラス名
     * @param string $file_contents 生成マイグレーションファイル内容
     * @return void
     */
    private function saveMigrationFile(string $class_name, string $file_contents): void
    {
        $output_dir = $this->configure['output_dir'];
        file_put_contents(
            sprintf(
                '%s/%s.class.php',
                $output_dir,
                $class_name
            ),
            $file_contents
        );
    }



    /**
     * マイグレーションクラスのインスタンス取得
     *
     * @param string      $output_dir マイグレーションファイル格納マイグレーションファイル
     * @param string      $filename   ファイル名
     * @param string|null $version    バージョン指定
     * @return Item|null
     */
    private function callInstance(string $output_dir, string $filename,  string $version = null): ?Item
    {
        // マイグレーションファイルパス
        $class_path = $output_dir . $filename;

        // ファイルでなければスルー
        if (false === is_file($class_path))
        {
            return null;
        }

        // バージョン指定時に、対象バージョン以外だったらスルー
        if (false === is_null($version) && false === strpos($filename, $version))
        {
            return null;
        }

        // マイグレーションクラス名
        $class_name = str_replace('.class.php', '', $filename);

        // ファイルであれば読み込み
        include_once($class_path);
        return new $class_name($this->dsn);
    }
}