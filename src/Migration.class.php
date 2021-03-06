<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Configure\Configurable;
use Citrus\Database\DSN;
use Citrus\Migration\Item;
use Citrus\Migration\VersionManager;
use Citrus\Variable\Singleton;

/**
 * マイグレーション処理
 */
class Migration extends Configurable
{
    use Singleton;

    /** @var string 生成 */
    const ACTION_GENERATE = 'generate';

    /** @var string マイグレーションUP */
    const ACTION_MIGRATION_UP = 'up';

    /** @var string マイグレーションDOWN */
    const ACTION_MIGRATION_DOWN = 'down';

    /** @var string マイグレーションREBIRTH */
    const ACTION_MIGRATION_REBIRTH = 'rebirth';

    /** @var VersionManager バージョンマネージャー */
    protected $versionManager;



    /**
     * {@inheritDoc}
     */
    public function loadConfigures(array $configures = []): Configurable
    {
        // 設定配列の読み込み
        parent::loadConfigures($configures);

        // 出力ファイル出力パスの設定
        self::setupOutputDirectory();

        // DSN情報
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        // バージョンマネージャー
        $this->versionManager = new VersionManager($dsn);

        return $this;
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
        return <<<SQL

SQL;
    }

    public function down()
    {
        return <<<SQL

SQL;
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
     * @throws CitrusException
     */
    public function up(string $version = null): void
    {
        // 出力パス
        $output_dir = $this->configures['output_dir'];

        // 対象ファイルの取得
        $migration_files = scandir($output_dir);

        // 対象の場合は実行
        /** @var string $one ex. Citrus_XXXXXXXXXXXXXX_CreateTableXXXXXs.class.php */
        foreach ($migration_files as $one)
        {
            /** @var Item $instance */
            $instance = $this->callInstance($output_dir, $one, $version);
            if (true === is_null($instance))
            {
                continue;
            }

            // バージョンアップ
            $this->versionManager->up($instance);
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
        // 出力パス
        $output_dir = $this->configures['output_dir'];

        // 対象ファイルの取得
        $migration_files = scandir($output_dir);
        $migration_files = array_reverse($migration_files);

        // 対象の場合は実行
        /** @var string $one ex. Citrus_XXXXXXXXXXXXXX_CreateTableXXXXXs.class.php */
        foreach ($migration_files as $one)
        {
            /** @var Item $instance */
            $instance = $this->callInstance($output_dir, $one, $version);
            if (true === is_null($instance))
            {
                continue;
            }

            // バージョンダウン
            $this->versionManager->down($instance);
        }
    }



    /**
     * マイグレーションREBIRTHの実行
     *
     * @param string|null $version バージョン指定(指定がなければ全部)
     * @throws \Exception
     */
    public function rebirth(string $version = null)
    {
        // DOWN
        $this->down($version);
        // UP
        $this->up($version);
    }



    /**
     * {@inheritDoc}
     */
    protected function configureKey(): string
    {
        return 'migration';
    }



    /**
     * {@inheritDoc}
     */
    protected function configureDefaults(): array
    {
        return [
            'mode' => 0755,
            'owner' => posix_getpwuid(posix_geteuid())['name'],
            'group' => posix_getgrgid(posix_getegid())['name'],
        ];
    }



    /**
     * {@inheritDoc}
     */
    protected function configureRequires(): array
    {
        return [
            'database',
            'mode',
            'owner',
            'group',
            'output_dir',
        ];
    }



    /**
     * 出力ファイル格納ディレクトリパスの設定
     *
     * @return void
     */
    private function setupOutputDirectory(): void
    {
        // 出力ディレクトリ
        $output_dir = $this->configures['output_dir'];

        // ディレクトリがなければ生成
        if (false === file_exists($output_dir))
        {
            mkdir($output_dir);
            chmod($output_dir, $this->configures['mode']);
            chown($output_dir, $this->configures['owner']);
            chgrp($output_dir, $this->configures['group']);
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
        $output_dir = $this->configures['output_dir'];
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
    private function callInstance(string $output_dir, string $filename, string $version = null): ?Item
    {
        // マイグレーションファイルパス
        $class_path = sprintf('%s/%s', $output_dir, $filename);

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
        return new $class_name();
    }
}
