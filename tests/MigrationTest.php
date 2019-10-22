<?php
/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

use Citrus\Citrus;
use Citrus\CitrusException;
use Citrus\Database\DSN;
use Citrus\Migration;
use Citrus\Migration\Item;
use PHPUnit\Framework\TestCase;

/**
 * マイグレーション処理のテスト
 */
class MigrationTest extends TestCase
{
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
        if (true === is_dir($this->output_dir))
        {
            // 内部ファイルも消す
            $files = scandir($this->output_dir);
            foreach ($files as $file)
            {
                $file_path = sprintf('%s/%s', $this->output_dir, $file);
                if (true === is_file($file_path))
                {
                    unlink($file_path);
                }
            }

            // ディレクトリを消す
            rmdir($this->output_dir);
        }
    }



    /**
     * @test
     * @throws CitrusException
     */
    public function 設定ファイル通りにディレクトリを生成()
    {
        // インスタンス生成と実行
        $migration = new Migration($this->configure);
        Closure::bind(function () use ($migration) {
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
        $dsn = Closure::bind(function () use ($migration) {
            return $migration->dsn;
        }, $this, Migration::class)->__invoke();
        // 検算用DBファイルの作成
        mkdir($this->output_dir);
        touch($this->sqlite_file);
        // 検算用PDO
        $pdo = new PDO($dsn->toString());
        // 検算用クエリ
        $query = 'SELECT user_id, name FROM users;';

        // マイグレーションの正方向実行
        $migrationItem = new Citrus_20190101000000_CreateTableUsers($dsn);
        $migrationItem->up();

        // テーブル作成が成功していればSELECT文が発行できる
        $this->assertNotFalse($pdo->query($query));

        // マイグレーションの逆方向実行
        $migrationItem->down();

        // テーブル削除が成功していればSELECT文が発行できない
        $this->assertFalse($pdo->query($query));
    }
}

/**
 * テスト用マイグレーションクラス
 */
class Citrus_20190101000000_CreateTableUsers extends Item
{

    /**
     * migration.sh up
     */
    public function up()
    {
        $this->execute(<<<SQL
CREATE TABLE users (
    `user_id` int NOT NULL,
    `name` TEXT
);
SQL
        );
    }



    /**
     * migration.sh down
     */
    public function down()
    {
        $this->execute(<<<SQL
DROP TABLE users;
SQL
        );
    }
}
