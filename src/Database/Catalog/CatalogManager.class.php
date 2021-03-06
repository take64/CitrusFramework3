<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Catalog;

use Citrus\Database\Catalog\Driver\Postgres;
use Citrus\Database\Catalog\Driver\Sqlite;
use Citrus\Database\DSN;
use Citrus\Struct;

/**
 * データベースカタログ管理
 */
class CatalogManager extends Struct
{
    /** @var CatalogDriver DBタイプ別のクラス */
    private $catalogDriver;



    /**
     * constructor.
     *
     * @param DSN $dsn
     */
    public function __construct(DSN $dsn)
    {
        // PostgreSQL
        if (true === $dsn->isPostgreSQL())
        {
            $this->catalogDriver = new Postgres($dsn);
        }
        // SQLite
        else if (true === $dsn->isSQLite())
        {
            $this->catalogDriver = new Sqlite($dsn);
        }
    }




    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function tableColumns(string $table_name): array
    {
        return $this->catalogDriver->tableColumns($table_name);
    }



    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function columnComments(string $table_name): array
    {
        return $this->catalogDriver->columnComments($table_name);
    }



    /**
     * テーブルのプライマリキー定義の取得
     *
     * @param string $table_name テーブル名
     * @return string[]
     */
    public function primaryKeys(string $table_name): array
    {
        return $this->catalogDriver->primaryKeys($table_name);
    }
}
