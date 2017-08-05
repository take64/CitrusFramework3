<?php
/**
 * Client.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Sqlmap
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;


use Citrus\Citrus;
use Citrus\CitrusConfigure;
use Citrus\Database\CitrusDatabaseColumn;

class CitrusSqlmapClient
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
     * @throws CitrusSqlmapException
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
                    Citrus::$DIR_INTEGRATION_SQLMAP,
                    $this->sqlmap_id
                );
                $this->sqlmap_path = $default_sqlmap_path;
            }
        }

        // sqlmap_pathが設定できなかった場合
        if (is_null($this->sqlmap_path) === true)
        {
            throw new CitrusSqlmapException('SQLMAPが指定されていません。');
        }
    }



    /**
     * Executes a mapped SQL INSERT statement.
     *
     * @param string               $id
     * @param CitrusDatabaseColumn $parameter
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function insert(string $id, CitrusDatabaseColumn $parameter) : bool
    {
        try
        {
            $insert = new CitrusSqlmapParser();
            $insert->parse($this->sqlmap_path, 'insert', $id, $parameter);
            return CitrusSqlmapExecutor::insert($insert->statement, $insert->parameter_list);
        }
        catch(CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * Executes a mapped SQL UPDATE statement.
     *
     * @param string               $id
     * @param CitrusDatabaseColumn $parameter
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function update(string $id, CitrusDatabaseColumn $parameter) : bool
    {
        try
        {
            $update = new CitrusSqlmapParser();
            $update->parse($this->sqlmap_path, 'update', $id, $parameter);
            return CitrusSqlmapExecutor::update($update->statement, $update->parameter_list);
        }
        catch(CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * Executes a mapped SQL DELETE statement.
     *
     * @param string               $id
     * @param CitrusDatabaseColumn $parameter
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function delete(string $id, CitrusDatabaseColumn $parameter) : bool
    {
        try
        {
            $delete = new CitrusSqlmapParser();
            $delete->parse($this->sqlmap_path, 'delete', $id, $parameter);
            return CitrusSqlmapExecutor::delete($delete->statement, $delete->parameter_list);
        }
        catch(CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * Executes a mapped SQL SELECT statement that returns data to populate
     *
     * @param string               $id
     * @param CitrusDatabaseColumn $parameter
     * @return array|CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function queryForList(string $id, CitrusDatabaseColumn $parameter) : array
    {
        try
        {
            $select = new CitrusSqlmapParser();
            $select->parse($this->sqlmap_path, 'select', $id, $parameter);
            return CitrusSqlmapExecutor::select($select->statement, $select->parameter_list);
        }
        catch(CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * Executes a mapped SQL SELECT statement that returns data to populate
     *
     * @param string               $id
     * @param CitrusDatabaseColumn $parameter
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function queryForObject(string $id, CitrusDatabaseColumn $parameter) : CitrusDatabaseColumn
    {
        try
        {
            /** @var CitrusSqlmapCondition $parameter */
            if (is_null($parameter) === true)
            {
                $parameter = new CitrusDatabaseColumn();
            }
            if (is_null($parameter->limit) === true)
            {
                $parameter->limit = 1;
            }

            $select = new CitrusSqlmapParser();
            $select->parse($this->sqlmap_path, 'select', $id, $parameter);
            $result = CitrusSqlmapExecutor::select($select->statement, $select->parameter_list);
            return count($result) > 0 ? $result[0] : null;
        }
        catch(CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * Executes a mapped SQL GENERAL statement that returns data to populate
     *
     * @param string               $id
     * @param CitrusDatabaseColumn $parameter
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function statement(string $id, CitrusDatabaseColumn $parameter) : CitrusDatabaseColumn
    {
        try
        {
            $statement = new CitrusSqlmapParser();
            $statement->parse($this->sqlmap_path, 'statement', $id, $parameter);
            $result = CitrusSqlmapExecutor::select($statement->statement, $statement->parameter_list);
            return count($result) > 0 ? $result[0] : null;
        }
        catch(CitrusSqlmapException $e)
        {
            throw $e;
        }
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
        CitrusSqlmapExecutor::begin();
    }



    /**
     * commit transaction
     */
    public function commit()
    {
        CitrusSqlmapExecutor::commit();
    }



    /**
     * rollback transaction
     */
    public function rollback()
    {
        CitrusSqlmapExecutor::rollback();
    }
}