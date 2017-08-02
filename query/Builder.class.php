<?php
/**
 * Builder.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     .
 * @subpackage  .
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Query;


use Citrus\CitrusNVL;
use Citrus\Database\CitrusDatabaseColumn;
use Citrus\Database\CitrusDatabaseDSN;
use Citrus\Sqlmap\CitrusSqlmapClient;
use Citrus\Sqlmap\CitrusSqlmapCondition;
use Citrus\Sqlmap\CitrusSqlmapExecutor;
use Citrus\Sqlmap\CitrusSqlmapStatement;

class CitrusQueryBuilder
{
    /** @var string */
    const QUERY_TYPE_SELECT = 'select';

    /** @var string */
    const QUERY_TYPE_INSERT = 'insert';

    /** @var string */
    const QUERY_TYPE_UPDATE = 'update';

    /** @var string */
    const QUERY_TYPE_DELETE = 'delete';

    /** @var CitrusSqlmapStatement $statement */
    public $statement = null;

    /** @var array $parameter_list */
    public $parameter_list = [];

    /** @var string $query_type */
    public $query_type = self::QUERY_TYPE_SELECT;



    /**
     * build select statement
     *
     * @param string                    $table_name
     * @param array|null                $columns
     * @param CitrusDatabaseColumn|null $condition
     * @return CitrusQueryBuilder
     */
    public function select(string $table_name, array $columns = null, CitrusDatabaseColumn $condition = null) : CitrusQueryBuilder
    {
        // クエリタイプ
        $this->query_type = self::QUERY_TYPE_SELECT;

        // ステートメント
        $this->statement = new CitrusSqlmapStatement();

        // カラム列挙
        $select_context = CitrusNVL::replace($columns, '*', implode(', ', $columns));

        // ベースクエリー
        $query = sprintf('SELECT %s FROM %s', $select_context, $table_name);

        // 検索条件,取得条件
        if (is_null($condition) === false)
        {
            // 検索条件
            $properties = $condition->properties();
            $wheres = [];
            $parameters = [];
            foreach ($properties as $ky => $vl)
            {
                if (is_null($vl) === true)
                {
                    continue;
                }

                $bind_ky = sprintf(':%s', $ky);
                $wheres[] = sprintf('%s = %s', $ky, $bind_ky);
                $parameters[$bind_ky] = $vl;
            }
            // 検索条件がある場合
            if (empty($wheres) === false)
            {
                $query = sprintf('%s WHERE %s', $query, implode(' AND ', $wheres));
            }

            // 取得条件
            $condition_traits = class_uses($condition);
            if (array_key_exists('CitrusSqlmapCondition', $condition_traits) === true)
            {
                /** @var CitrusSqlmapCondition $condition */

                // 順序
                if (is_null($condition->orderby) === false)
                {
                    $query = sprintf('%s ORDER BY %s', $query, $condition->orderby);
                }

                // 制限
                if (is_null($condition->limit) === false)
                {
                    $ky = 'limit';
                    $query = sprintf('%s LIMIT :%s', $query, $ky);
                    $parameters[$ky] = $condition->limit;
                }
                if (is_null($condition->offset) === false)
                {
                    $ky = 'offset';
                    $query = sprintf('%s OFFSET :%s', $query, $ky);
                    $parameters[$ky] = $condition->offset;
                }
            }
        }

        $this->statement->query = $query;
        $this->parameter_list = $parameters;

        return $this;
    }



    /**
     * build insert statement
     *
     * @param string               $table_name
     * @param CitrusDatabaseColumn $value
     * @return CitrusQueryBuilder
     */
    public function insert(string $table_name, CitrusDatabaseColumn $value) : CitrusQueryBuilder
    {
        // クエリタイプ
        $this->query_type = self::QUERY_TYPE_INSERT;

        // ステートメント
        $this->statement = new CitrusSqlmapStatement();

        // 登録情報
        $columns = [];
        $parameters = [];
        $properties = $value->properties();
        foreach ($properties as $ky => $vl)
        {
            if (is_null($vl) === true)
            {
                continue;
            }

            $bind_ky = sprintf(':%s', $ky);
            $columns[$ky] = $bind_ky;
            $parameters[$bind_ky] = $vl;
        }

        // クエリ
        $query = sprintf('INSERT INTO %s (%s) VALUES (%s);',
            $table_name,
            implode(',' , array_keys($columns)),
            implode(',' , array_values($columns))
            );

        $this->statement->query = $query;
        $this->parameter_list = $parameters;

        return $this;
    }



    /**
     * build update statement
     *
     * @param string               $table_name
     * @param CitrusDatabaseColumn $value
     * @param CitrusDatabaseColumn $condition
     * @return CitrusQueryBuilder
     */
    public function update(string $table_name, CitrusDatabaseColumn $value, CitrusDatabaseColumn $condition) : CitrusQueryBuilder
    {
        // クエリタイプ
        $this->query_type = self::QUERY_TYPE_UPDATE;

        // ステートメント
        $this->statement = new CitrusSqlmapStatement();

        // 登録情報
        $columns = [];
        $parameters = [];
        $properties = $value->properties();
        foreach ($properties as $ky => $vl)
        {
            if (is_null($vl) === true)
            {
                continue;
            }

            $bind_ky = sprintf(':%s', $ky);
            $columns[$ky] = sprintf('%s = %s', $ky, $bind_ky);
            $parameters[$bind_ky] = $vl;
        }
        // 登録条件
        $wheres = [];
        $properties = $condition->properties();
        foreach ($properties as $ky => $vl)
        {
            if (is_null($vl) === true)
            {
                continue;
            }

            $bind_ky = sprintf(':condition_%s', $ky);
            $wheres[$ky] = sprintf('%s = %s', $ky, $bind_ky);;
            $parameters[$bind_ky] = $vl;
        }

        // クエリ
        $query = sprintf('UPDATE %s SET %s (%s) WHERE (%s);',
            $table_name,
            implode(',' , array_keys($columns)),
            implode(',' , array_values($wheres))
        );

        $this->statement->query = $query;
        $this->parameter_list = $parameters;

        return $this;
    }



    /**
     * build delete statement
     *
     * @param string               $table_name
     * @param CitrusDatabaseColumn $condition
     * @return CitrusQueryBuilder
     */
    public function delete(string $table_name, CitrusDatabaseColumn $condition) : CitrusQueryBuilder
    {
        // クエリタイプ
        $this->query_type = self::QUERY_TYPE_UPDATE;

        // ステートメント
        $this->statement = new CitrusSqlmapStatement();

        // 登録情報
        $wheres = [];
        $parameters = [];
        $properties = $condition->properties();
        foreach ($properties as $ky => $vl)
        {
            if (is_null($vl) === true)
            {
                continue;
            }

            $bind_ky = sprintf(':%s', $ky);
            $wheres[$ky] = sprintf('%s = %s', $ky, $bind_ky);
            $parameters[$bind_ky] = $vl;
        }

        // クエリ
        $query = sprintf('DELETE FROM %s WHERE %s;',
            $table_name,
            implode(',' , array_values($wheres))
        );

        $this->statement->query = $query;
        $this->parameter_list = $parameters;

        return $this;
    }



    /**
     * execute
     *
     * @return array|bool|CitrusDatabaseColumn[]|null
     */
    public function execute()
    {
        $result = null;

        switch ($this->query_type)
        {
            // select
            case self::QUERY_TYPE_SELECT :
                $result = CitrusSqlmapExecutor::select($this->statement, $this->parameter_list);
                break;
            // insert
            case self::QUERY_TYPE_INSERT :
                $result = CitrusSqlmapExecutor::insert($this->statement, $this->parameter_list);
                break;
            // update
            case self::QUERY_TYPE_UPDATE :
                $result = CitrusSqlmapExecutor::update($this->statement, $this->parameter_list);
                break;
            // delete
            case self::QUERY_TYPE_DELETE :
                $result = CitrusSqlmapExecutor::delete($this->statement, $this->parameter_list);
                break;
        }

        return $result;
    }
}