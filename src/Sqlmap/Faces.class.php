<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Database\Column;
use Citrus\Database\ResultSet\ResultSet;
use Citrus\Variable\Singleton;

/**
 * Facesフレームで使用する処理
 */
class Faces extends Crud
{
    use Singleton;



    /**
     * FACES向けの選択リスト
     *
     * @param Column $condition
     * @return ResultSet
     * @throws SqlmapException
     */
    public function facesSelection(Column $condition): ResultSet
    {
        $parser = Parser::generate($this->sqlmap_path, 'facesSelection', $condition, $this->connection->dsn);
        return $this->select($parser);
    }



    /**
     * FACES向けのサマリークエリ
     *
     * @param Column $condition
     * @return ResultSet
     * @throws SqlmapException
     */
    public function facesSummary(Column $condition): ResultSet
    {
        $parser = Parser::generate($this->sqlmap_path, 'facesSummary', $condition, $this->connection->dsn);
        return $this->select($parser);
    }



    /**
     * FACES向けの詳細クエリ
     *
     * @param Column $condition
     * @return ResultSet
     * @throws SqlmapException
     */
    public function facesDetail(Column $condition): ResultSet
    {
        $parser = Parser::generate($this->sqlmap_path, 'facesDetail', $condition, $this->connection->dsn);
        return $this->select($parser);
    }
}
