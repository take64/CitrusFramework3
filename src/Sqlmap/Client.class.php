<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Configure;
use Citrus\Database\Column;

class Client
{
    /** @var string sqlmap file path */
    protected $sqlmap_path = null;

    /** @var string sqlmap file id */
    protected $sqlmap_id = '';

    /** @var bool validation flag */
    private $flg_validate = true;

    /** @var bool message flag */
    private $flg_message = true;



    /**
     * constructor
     *
     * @param string|null $sqlmap_path
     * @throws SqlmapException
     */
    public function __construct(string $sqlmap_path = null)
    {
        // 下位クラスでプロパティ指定されていない場合
        if (is_null($this->sqlmap_path) === true)
        {
            // sqlmapファイルが指定された場合
            if (is_null($sqlmap_path) === false)
            {
                // ファイルが存在すれば設定
                if (file_exists($sqlmap_path) === true)
                {
                    $this->sqlmap_path = $sqlmap_path;
                }
            }
            // sqlmapファイルが指定されていない場合にsqlmap_idがあれば設定する
            else
            {
                // デフォルト設定を適用する
                $default_sqlmap_path = sprintf(
                    '%s/%s.xml',
                    Configure::$DIR_INTEGRATION_SQLMAP,
                    $this->sqlmap_id
                );
                $this->sqlmap_path = $default_sqlmap_path;
            }
        }

        // sqlmap_pathが設定できなかった場合
        if (is_null($this->sqlmap_path) === true)
        {
            throw new SqlmapException('SQLMAPが指定されていません。');
        }
    }



    /**
     * Executes a mapped SQL INSERT statement.
     *
     * @param string               $id
     * @param Column $parameter
     * @return bool
     * @throws SqlmapException
     */
    public function insert(string $id, Column $parameter) : bool
    {
        $parser = Parser::generateParser($this->sqlmap_path, 'insert', $id, $parameter);
        return Executor::insert($parser->statement, $parser->parameter_list);
    }



    /**
     * Executes a mapped SQL UPDATE statement.
     *
     * @param string               $id
     * @param Column $parameter
     * @return bool
     * @throws SqlmapException
     */
    public function update(string $id, Column $parameter) : bool
    {
        $parser = Parser::generateParser($this->sqlmap_path, 'update', $id, $parameter);
        return Executor::update($parser->statement, $parser->parameter_list);
    }



    /**
     * Executes a mapped SQL DELETE statement.
     *
     * @param string               $id
     * @param Column $parameter
     * @return bool
     * @throws SqlmapException
     */
    public function delete(string $id, Column $parameter) : bool
    {
        $parser = Parser::generateParser($this->sqlmap_path, 'delete', $id, $parameter);
        return Executor::delete($parser->statement, $parser->parameter_list);
    }



    /**
     * Executes a mapped SQL SELECT statement that returns data to populate
     *
     * @param string                  $id        クエリID
     * @param Column    $parameter パラメーター
     * @param Parser|null $parser    パーサーのキャッシュ
     * @return array|Column[]
     * @throws SqlmapException
     */
    public function queryForList(string $id, Column $parameter, Parser $parser = null) : array
    {
        $parser = $parser ?: Parser::generateParser($this->sqlmap_path, 'select', $id, $parameter);
        return Executor::select($parser->statement, $parser->parameter_list);
    }



    /**
     * Executes a mapped SQL SELECT statement that returns data to populate
     *
     * @param string                    $id        クエリID
     * @param Column|null $_parameter パラメーター
     * @param Parser|null   $parser    パーサーのキャッシュ
     * @return Column|null
     * @throws SqlmapException
     */
    public function queryForObject(string $id, Column $_parameter = null, Parser $parser = null)
    {
        /** @var Condition $parameter */
        $parameter = (is_null($_parameter) === true ? new Column() : clone $_parameter);
        if (is_null($parameter->limit) === true)
        {
            $parameter->limit = 1;
        }

        $parser = $parser ?: Parser::generateParser($this->sqlmap_path, 'select', $id, $parameter);
        $result = Executor::select($parser->statement, $parser->parameter_list);
        return count($result) > 0 ? $result[0] : null;
    }



    /**
     * Executes a mapped SQL GENERAL statement that returns data to populate
     *
     * @param string               $id
     * @param Column $parameter
     * @return Column|null
     * @throws SqlmapException
     */
    public function statement(string $id, Column $parameter)
    {
        $parser = Parser::generateParser($this->sqlmap_path, 'statement', $id, $parameter);
        $result = Executor::select($parser->statement, $parser->parameter_list);
        return count($result) > 0 ? $result[0] : null;
    }



    /**
     * vague transaction enable
     */
    public function enableVague()
    {
        $this->disableValidate();
        $this->disableMessage();
    }



    /**
     * vague transaction disable
     */
    public function disableVague()
    {
        $this->enableValidate();
        $this->enableMessage();
    }



    /**
     * validate enable
     */
    public function enableValidate()
    {
        $this->flg_validate = true;
    }



    /**
     * validate disable
     */
    public function disableValidate()
    {
        $this->flg_validate = false;
    }



    /**
     * get validate flag
     *
     * @return bool
     */
    public function isValidate() : bool
    {
        return $this->flg_validate;
    }



    /**
     * message enable
     */
    public function enableMessage()
    {
        $this->flg_message = true;
    }



    /**
     * message disable
     */
    public function disableMessage()
    {
        $this->flg_message = false;
    }



    /**
     * get validation flag
     *
     * @return bool
     */
    public function isMessage() : bool
    {
        return $this->flg_message;
    }



    /**
     * begin transaction
     */
    public function begin()
    {
        Executor::begin();
    }



    /**
     * commit transaction
     */
    public function commit()
    {
        Executor::commit();
    }



    /**
     * rollback transaction
     */
    public function rollback()
    {
        Executor::rollback();
    }
}