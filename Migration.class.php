<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Database\DSN;
use Citrus\Migration\Item;

class Migration
{
    /** @var string 生成 */
    const ACTION_GENERATE = 'generate';

    /** @var string マイグレーションUP */
    const ACTION_MIGRATION = 'migration.sh';

    /** @var string マイグレーションUP */
    const ACTION_MIGRATION_UP = 'up';

    /** @var string マイグレーションDOWN */
    const ACTION_MIGRATION_DOWN = 'down';

    /** @var string マイグレーションREBIRTH */
    const ACTION_MIGRATION_REBIRTH = 'rebirth';



    /**
     * マイグレーションファイル生成
     *
     * @param string $directory     アプリディレクトリ名
     * @param string $generate_name 生成ファイル名
     */
    public static function generate(string $directory, string $generate_name)
    {
        // 生成時間
        $timestamp = Citrus::$TIMESTAMP_CHAR14;

        // マイグレーションディレクトリパス
        $migration_directory_path = self::callMigrationDirectoryPath($directory);

        // 対象テーブル名
        $object_name = $generate_name;
        $object_name = str_replace(['CreateTable', 'DropTable', 'AlterTable', 'CreateView', 'DropView' ], '', $object_name);
        $object_name = strtolower($object_name);

        // マイグレーション内容
        $migration_file_string = <<<EOT
<?php
/**
 * generated Citrus Migration file at {#date#}
 */

use Citrus\Migration\CitrusMigrationItem;

class {#class_name#} extends CitrusMigrationItem
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
        $migration_file_string = str_replace('{#date#}', Citrus::$TIMESTAMP_FORMAT, $migration_file_string);
        $migration_file_string = str_replace('{#object_name#}', $object_name, $migration_file_string);

        // ファイル名
        $generate_class_name = sprintf('Citrus_%s_%s', $timestamp, $generate_name);
        $migration_file_string = str_replace('{#class_name#}', $generate_class_name,$migration_file_string);

        // generate
        self::saveMigrationFile($migration_directory_path, $generate_class_name, $migration_file_string);
    }



    /**
     * マイグレーションの実行
     *
     * @param string              $directory アプリディレクトリ名
     * @param DSN[] $dsns      マイグレーション対象DSNリスト
     * @param string|null         $version   バージョン指定
     */
    public static function up(string $directory, array $dsns, string $version = null)
    {
        // マイグレーションディレクトリパス
        $migration_directory_path = self::callMigrationDirectoryPath($directory);

        // 対象ファイルの取得
        $migration_files = scandir($migration_directory_path);

        // DSNの数だけやる
        foreach ($dsns as $dsn)
        {
            // 対象の場合は実行
            /** @var string $one ex. Citrus_XXXXXXXXXXXXXX_CreateTableXXXXXs.class.php */
            foreach ($migration_files as $one)
            {
                // マイグレーションファイルパス
                $class_path = $migration_directory_path . $one;

                // ファイルでなければスルー
                if (is_file($class_path) === false)
                {
                    continue;
                }

                // バージョン指定時に、対象バージョン以外だったらスルー
                if (is_null($version) === false && strpos($one, $version) === false)
                {
                    continue;
                }

                // マイグレーションクラス名
                $class_name = str_replace('.class.php', '', $one);

                // ファイルであれば読み込み
                include_once($class_path);

                /** @var Item $instance */
                $instance = new $class_name($dsn);

                // 実行
                $instance->up($dsn);
            }
        }
    }



    /**
     * マイグレーションDOWNの実行
     *
     * @param string              $directory アプリディレクトリ名
     * @param DSN[] $dsns      マイグレーション対象DSNリスト
     * @param string|null         $version   バージョン指定
     */
    public static function down(string $directory, array $dsns, string $version = null)
    {
        // マイグレーションディレクトリパス
        $migration_directory_path = self::callMigrationDirectoryPath($directory);

        // 対象ファイルの取得
        $migration_files = scandir($migration_directory_path);
        $migration_files = array_reverse($migration_files);

        // DSNの数だけやる
        foreach ($dsns as $dsn)
        {
            // 対象の場合は実行
            /** @var string $one ex. Citrus_XXXXXXXXXXXXXX_CreateTableXXXXXs.class.php */
            foreach ($migration_files as $one)
            {
                // マイグレーションファイルパス
                $class_path = $migration_directory_path . $one;

                // ファイルでなければスルー
                if (is_file($class_path) === false)
                {
                    continue;
                }

                // バージョン指定時に、対象バージョン以外だったらスルー
                if (is_null($version) === false && strpos($one, $version) === false)
                {
                    continue;
                }

                // マイグレーションクラス名
                $class_name = str_replace('.class.php', '', $one);

                // ファイルであれば読み込み
                include_once($class_path);

                /** @var Item $instance */
                $instance = new $class_name($dsn);

                // 実行
                $instance->down($dsn);
            }
        }
    }



    /**
     * マイグレーションREBIRTHの実行
     *
     * @param string              $directory アプリディレクトリ名
     * @param DSN[] $dsns      マイグレーション対象DSNリスト
     * @param string|null         $version   バージョン指定
     */
    public static function rebirth(string $directory, array $dsns, string $version = null)
    {
        // DOWN
        self::down($directory, $dsns, $version);
        // UP
        self::up($directory, $dsns, $version);
    }



    /**
     * マイグレーションファイル格納ディレクトリパスの取得
     *
     * @param string $directory
     * @return string
     */
    private static function callMigrationDirectoryPath(string $directory) : string
    {
        $path = sprintf('%s/.migration/', $directory);

        // ディレクトリがなければ生成
        if (file_exists($path) === false)
        {
            mkdir($path);
            chmod($path, 0666);
            chown($path, 'wwwrun');
            chgrp($path, 'www');
        }

        return $path;
    }



    /**
     * 生成したマイグレーションファイルの保存
     *
     * @param string $migration_directory_path マイグレーションファイル格納ディレクトリパス
     * @param string $generate_class_name      生成マイグレーションクラス名
     * @param string $migration_file_string    生成マイグレーションファイル内容
     */
    private static function saveMigrationFile(string $migration_directory_path,
                                              string $generate_class_name,
                                              string $migration_file_string)
    {
        file_put_contents(
            sprintf(
                '%s%s.class.php',
                $migration_directory_path,
                $generate_class_name
            ),
            $migration_file_string
        );
    }
}