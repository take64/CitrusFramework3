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
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;


use Citrus\CitrusConfigure;
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
     * @param CitrusDatabaseColumn|null $condition
     * @param array|null                $columns
     * @return CitrusQueryBuilder
     */
    public function select(string $table_name, CitrusDatabaseColumn $condition = null, array $columns = null) : CitrusQueryBuilder
    {
        // クエリタイプ
        $this->query_type = self::QUERY_TYPE_SELECT;

        // ステートメント
        $this->statement = new CitrusSqlmapStatement();

        // カラム列挙
        $select_context = CitrusNVL::EmptyVL($columns, '*', function () use ($columns) {
            return implode(', ', $columns);
        });
//var_dump([$select_context, $condition->schema, $table_name]);
        // ベースクエリー
        $query = sprintf('SELECT %s FROM %s.%s', $select_context, $condition->schema, $table_name);

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
        $this->parameters = $parameters;

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

        // 自動補完
        $value->completeRegistColumn();

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
        $query = sprintf('INSERT INTO %s.%s (%s) VALUES (%s);',
            $value->schema,
            $table_name,
            implode(',' , array_keys($columns)),
            implode(',' , array_values($columns))
            );

        $this->statement->query = $query;
        $this->parameters = $parameters;

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

        // 自動補完
        $value->completeModifyColumn();

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
        $query = sprintf('UPDATE %s.%s SET %s WHERE %s;',
            $value->schema,
            $table_name,
            implode(', ' , array_values($columns)),
            implode(' AND ' , array_values($wheres))
        );

        $this->statement->query = $query;
        $this->parameters = $parameters;

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
        $query = sprintf('DELETE FROM %s.%s WHERE %s;',
            $condition->schema,
            $table_name,
            implode(',' , array_values($wheres))
        );

        $this->statement->query = $query;
        $this->parameters = $parameters;

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

        // optimize parameters
        $parameters = self::optimizeParameter($this->statement->query, $this->parameters);

        switch ($this->query_type)
        {
            // select
            case self::QUERY_TYPE_SELECT :
                $result = CitrusSqlmapExecutor::select($this->statement, $parameters);
                break;
            // insert
            case self::QUERY_TYPE_INSERT :
                $result = CitrusSqlmapExecutor::insert($this->statement, $parameters);
                break;
            // update
            case self::QUERY_TYPE_UPDATE :
                $result = CitrusSqlmapExecutor::update($this->statement, $parameters);
                break;
            // delete
            case self::QUERY_TYPE_DELETE :
                $result = CitrusSqlmapExecutor::delete($this->statement, $parameters);
                break;
        }

        return $result;
    }



    /**
     * クエリに定義されていないパラメータを消す
     *
     * @param string     $query
     * @param array|null $parameters
     * @return array
     */
    public static function optimizeParameter(string $query, array $parameters = null) : array
    {
        // パラメータがなければスルー
        if (is_null($parameters) === true)
        {
            return $parameters;
        }

        // conditionの削除
        unset($parameters[':condition']);

        // パラメータの最適化
        foreach ($parameters as $ky => $vl)
        {
            if (strpos($query, $ky) === false)
            {
                unset($parameters[$ky]);
            }
        }

        return $parameters;
    }
}