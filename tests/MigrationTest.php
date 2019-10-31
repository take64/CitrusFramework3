<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Citrus;
use Citrus\CitrusException;
use Citrus\Database\DSN;
use Citrus\Migration;
use PHPUnit\Framework\TestCase;
use Test\Sample\Citrus_20190101000000_CreateTableUsers;

/**
 * マイグレーション処理のテスト
 */
class MigrationTest extends TestCase
{
    use TestFile;

    /** @var string 出力ディレクトリ */
    private $output_dir;

    /** @var string SQLITEファイル */
    private $sqlite_file;

    /** @var array 設定配列 */
    private $configure;



    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // 出力ディレクトリ
        $this->output_dir = __DIR__ . '/.migration';
        $this->sqlite_file = $this->output_dir . '/test.sqlite';

        // 設定配列
        $this->configure = [
            'database' => [
                'type'      => 'sqlite',
                'hostname'  => $this->sqlite_file,
            ],
            'output_dir' => $this->output_dir,
            'mode' => 0755,
            'owner' => posix_getpwuid(posix_geteuid())['name'],
            'group' => posix_getgrgid(posix_getegid())['name'],
        ];
    }



    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        // ディレクトリがあったら削除
        $this->forceRemove($this->output_dir);
    }



    /**
     * @test
     * @throws CitrusException
     */
    public function 設定ファイル通りにディレクトリを生成()
    {
        // インスタンス生成と実行
        $migration = new Migration($this->configure);
        \Closure::bind(function () use ($migration) {
            $migration->setupOutputDirectory();
        }, $this, Migration::class)->__invoke();

        // ディレクトリができている
        $this->assertTrue(is_dir($this->output_dir));
    }



    /**
     * @test
     * @throws CitrusException
     */
    public function マイグレーション_ファイル生成()
    {
        // 生成用の名称引数
        $name = 'CreateTableUsers';

        // インスタンス生成と実行
        $migration = new Migration($this->configure);
        $migration->generate($name);

        // ファイルができている
        $output_file = sprintf('%s/Citrus_%s_%s.class.php', $this->output_dir, Citrus::$TIMESTAMP_CHAR14, $name);
        $this->assertTrue(is_file($output_file));
    }



    /**
     * @test
     * @throws CitrusException
     */
    public function マイグレーション_両方向実行()
    {
        // インスタンスの生成
        $migration = new Migration($this->configure);
        /** @var DSN $dsn */
        $dsn = new DSN();
        $dsn->bind($this->configure['database']);
        // 検算用PDO
        $pdo = new \PDO($dsn->toString());
        // バージョンマネージャー
        $versionManager = new Migration\VersionManager($dsn);

        // 検算用クエリ
        $query = 'SELECT user_id, name FROM users;';

        // マイグレーションの正方向実行
        $migrationItem = new Citrus_20190101000000_CreateTableUsers();
        $versionManager->up($migrationItem);

        // テーブル作成が成功していればSELECT文が発行できる
        $pdo->query($query);
        $this->assertNotFalse($pdo->query($query));

        // マイグレーションの逆方向実行
        $versionManager->down($migrationItem);

        // テーブル削除が成功していればSELECT文が発行できない
        $this->assertFalse($pdo->query($query));
    }



    /**
     * @test
     * @throws CitrusException
     */
    public function バージョン情報が取れる()
    {
        // インスタンスの生成
        $migration = new Migration($this->configure);
        /** @var DSN $dsn */
        $dsn = new DSN();
        $dsn->bind($this->configure['database']);

        // マイグレーションアイテムの生成
        $item = new Citrus_20190101000000_CreateTableUsers();
        // 検算
        $this->assertSame('20190101000000', $item->version());
    }
}
