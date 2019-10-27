<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Migration;

use Citrus\Command\Console;
use Citrus\Database\DSN;
use Citrus\Struct;
use PDO;

/**
 * マイグレーションバージョン管理
 */
class VersionManager extends Struct
{
    use Console;

    /** @var PDO DBハンドラ */
    private $handler;

    /** @var DSN DB接続情報 */
    private $dsn;



    /**
     * constructor.
     *
     * @param DSN $dsn
     */
    public function __construct(DSN $dsn)
    {
        $this->dsn = $dsn;
        $this->handler = new PDO($this->dsn->toStringWithAuth());
        // マイグレーションのセットアップ
        $this->setupMigration();
    }



    /**
     * マイグレーションのセットアップ
     */
    public function setupMigration()
    {
        // マイグレーション管理テーブルの生成
        $query = <<<SQL
CREATE TABLE IF NOT EXISTS {SCHEMA}cf_migrations (
    version_code CHARACTER VARYING(32) NOT NULL,
    migrated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE {SCHEMA}cf_migrations ADD CONSTRAINT pk_cf_migrations PRIMARY KEY (version);
SQL;
        $query = $this->replaceSchema($query);
        $this->handler->query($query);
    }



    /**
     * マイグレーションの正方向実行
     *
     * @param Item $item
     * @throws \Exception
     */
    public function up(Item $item)
    {
        // バージョン
        $version = $item->version();
        // クラス名
        $class_name = get_class($item);

        // ログ：バージョン操作開始
        $this->format('%s up. executing.', $class_name);

        // トランザクションで実行
        $this->transaction(function() use ($version, $class_name, $item) {
            // バージョンアップできるか
            if (false === $this->isUp($version))
            {
                // ログ：バージョン操作対象外
                $this->format('%s up. is already.', $class_name);
                return;
            }

            // 実行開始タイム
            $microsecond = microtime(true);

            // 正方向クエリ
            $query = $item->up();
            $result = $this->executeQuery($query);

            // 実行終了タイム
            $execute_microsecond = (microtime(true) - $microsecond);

            // バージョン情報の登録
            $this->registVersion($version);

            // ログ：実行結果
            $method = (true === $result ? 'success' : 'failure');
            $this->$method(sprintf('%s up. %s. %f μs.', $class_name, $method, $execute_microsecond));
        });
    }



    /**
     * マイグレーションの逆方向実行
     *
     * @param Item $item
     */
    public function down(Item $item)
    {
        // バージョン
        $version = $item->version();
        // クラス名
        $class_name = get_class($item);

        // ログ：バージョン操作開始
        $this->format('%s down. executing.', $class_name);

        // トランザクションで実行
        $this->transaction(function() use ($version, $class_name, $item) {
            // バージョンダウンできるか
            if (false === $this->isDown($version))
            {
                // ログ：バージョン操作対象外
                $this->format('%s down. is already.', $class_name);
                return;
            }

            // 実行開始タイム
            $microsecond = microtime(true);

            // クエリ実行
            $query = $item->down();
            $result = $this->executeQuery($query);

            // 実行終了タイム
            $execute_microsecond = (microtime(true) - $microsecond);

            // バージョン情報の削除
            $this->removeVersion($version);

            // ログ：実行結果
            $method = (true === $result ? 'success' : 'failure');
            $this->$method(sprintf('%s down. %s. %f μs.', $class_name, $method, $execute_microsecond));
        });
    }



    /**
     * 指定のバージョンの正方向実行が可能か
     *
     * @param string $version
     * @return bool 正方向実行可能(未UP状態)
     */
    private function isUp(string $version): bool
    {
        return (false === $this->existVersion($version));
    }



    /**
     * 指定のバージョンの逆方向実行が可能か
     *
     * @param string $version
     * @return bool 逆方向実行可能(UP済み状態)
     */
    private function isDown(string $version): bool
    {
        return (true === $this->existVersion($version));
    }



    /**
     * クエリの実行
     *
     * @param string $query 実行したいクエリー
     * @return bool true:成功,false:失敗
     */
    private function executeQuery(string $query): bool
    {
        // スキーマ置換
        $query = $this->replaceSchema($query);
        // クエリ実行
        $result = $this->handler->query($query);

        return (false === $result ? false : true);
    }



    /**
     * 指定のバージョンの実行ログが存在するか
     *
     * @param string $version チェックしたいバージョン
     * @return bool true:存在する,false:存在しない
     */
    private function existVersion(string $version): bool
    {
        $query = 'SELECT * FROM {SCHEMA}cf_migrations WHERE version_code = :version_code;';
        $query = $this->replaceSchema($query);
        $statement = $this->handler->prepare($query);
        $statement->execute([
            ':version_code' => $version,
        ]);
        // 件数が0を超える場合、対象バージョンが存在する
        return (0 < count($statement->fetchAll(PDO::FETCH_ASSOC)) ? true : false);
    }



    /**
     * バージョン情報の登録
     *
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    private function registVersion(string $version): bool
    {
        $query = 'INSERT INTO {SCHEMA}cf_migrations (version_code, migrated_at) VALUES (:version_code, :migrated_at);';
        $query = $this->replaceSchema($query);
        $statement = $this->handler->prepare($query);
        return $statement->execute([
            ':version_code' => $version,
            ':migrated_at' => (new \DateTime())->format('Y-m-d H:i:s T'),
        ]);
    }



    /**
     * バージョン情報の削除
     *
     * @param string $version
     * @return bool
     */
    private function removeVersion(string $version): bool
    {
        $query = 'DELETE FROM {SCHEMA}cf_migrations WHERE version_code = :version_code;';
        $query = $this->replaceSchema($query);
        $statement = $this->handler->prepare($query);
        return $statement->execute([
            ':version_code' => $version,
        ]);
    }



    /**
     * 簡易なトランザクション管理
     *
     * @param \Closure $transaction
     */
    private function transaction(\Closure $transaction)
    {
        // トランザクション開始
        $this->handler->beginTransaction();
        try
        {
            // 処理の実行
            $transaction();
            // 成功時コミット
            $this->handler->commit();
        }
        catch (\PDOException $e)
        {
            // 失敗時ロールバック
            $this->handler->rollBack();
        }
    }



    /**
     * スキーマ指定の置換
     *
     * @param string $query 置換対象文字列
     * @return string 置換済み文字列
     */
    public function replaceSchema(string $query): string
    {
        return str_replace('{SCHEMA}', $this->dsn->schema, $query);
    }
}
