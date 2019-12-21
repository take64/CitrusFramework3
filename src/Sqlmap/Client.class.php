<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Configure;
use Citrus\Database\Connection;
use Citrus\Sqlmap\ResultSet\ResultSet;

/**
 * SQLMAPのSQL実行クライアント
 */
class Client
{
    /** @var Connection */
    protected $connection;

    /** @var string SQLMAPのID */
    protected $sqlmap_id;

    /** @var string SQLMAPのパス */
    protected $sqlmap_path;



    /**
     * constructor.
     *
     * @param Connection  $connection  接続情報
     * @param string|null $sqlmap_path SQLMAPのファイルパス
     * @throws SqlmapException
     */
    public function __construct(Connection $connection, string $sqlmap_path = null)
    {
        $this->connection = $connection;
        // 接続もしてしまう
        $this->connection->connect();

        // SQLMAPパスのセットアップ
        $this->setupSqlmapPath($sqlmap_path);
    }



    /**
     * SELECT
     *
     * @param Parser $parser
     * @return ResultSet
     * @throws SqlmapException
     */
    public function select(Parser $parser): ResultSet
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($parser);

        return new ResultSet($statement, $parser->statement->result_class);
    }



    /**
     * INSERT
     *
     * @param Parser $parser
     * @return int
     * @throws SqlmapException
     */
    public function insert(Parser $parser): int
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($parser);

        // 実行
        $statement->execute();

        return $statement->rowCount();
    }



    /**
     * UPDATE
     *
     * @param Parser $parser
     * @return int
     * @throws SqlmapException
     */
    public function update(Parser $parser): int
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($parser);

        // 実行
        $statement->execute();

        return $statement->rowCount();
    }



    /**
     * DELETE
     *
     * @param Parser $parser
     * @return int
     * @throws SqlmapException
     */
    public function delete(Parser $parser): int
    {
        // 削除全実行はフレームワークとして許容しない(全実行する場合は条件を明示的につける ex.)WHERE 1=1)
        if (0 === count($parser->parameter_list))
        {
            throw new SqlmapException('削除条件が足りません、削除要求をキャンセルしました。');
        }

        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($parser);

        // 実行
        $statement->execute();

        return $statement->rowCount();
    }



    /**
     * プリペアとパラメータ設定
     *
     * @param Parser $parser
     * @return \PDOStatement
     * @throws SqlmapException
     */
    private function prepareAndBind(Parser $parser): \PDOStatement
    {
        // ハンドル
        $handle = $this->connection->callHandle();

        // プリペア実行
        $statement = $handle->prepare($parser->statement->query);
        if (false === $statement)
        {
            throw SqlmapException::pdoErrorInfo($handle->errorInfo());
        }

        // パラメータ設定
        foreach ($parser->parameter_list as $ky => $vl)
        {
            $statement->bindValue($ky, $vl);
        }

        return $statement;
    }



    /**
     * SQLMAPパスのセットアップ
     *
     * @param string|null $sqlmap_path
     * @throws SqlmapException
     */
    public function setupSqlmapPath(?string $sqlmap_path): void
    {
        // SQLMAPのパスが指定されていない場合
        if (true === is_null($sqlmap_path))
        {
            // SQLMAPのIDから生成
            $sqlmap_path = sprintf('%s/%s.xml', Configure::$DIR_INTEGRATION_SQLMAP, $this->sqlmap_id);
            // 再起して設定
            $this->setupSqlmapPath($sqlmap_path);
            return;
        }

        // SQLMAPのパスが指定されている場合
        // ファイルが存在する場合
        if (true === file_exists($sqlmap_path))
        {
            // 設定して終わり
            $this->sqlmap_path = $sqlmap_path;
            return;
        }

        // 見つからない
        throw new SqlmapException('SQLMAPが指定されていません。');
    }
}
