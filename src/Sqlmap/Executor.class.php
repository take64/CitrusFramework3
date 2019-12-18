<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Configure;
use Citrus\Database\Column;
use Citrus\Database\DSN;
use Citrus\Database\Result;
use PDO;
use PDOException;
use PDOStatement;

class Executor
{
    /** @var PDO db connection handler */
    public static $HANDLER = null;

    /** @var bool transaction flg */
    public static $IS_TRANSACTIONS = false;

    /** @var bool db connection flg */
    private static $IS_CONNECTION = false;

    /** @var array db prepare cache */
    private static $_prepareQueries = [];

    /** @var array db prepare cache */
    private static $_prepareStatements = [];



    /**
     * connect db connection
     *
     * @throws SqlmapException
     */
    public static function connect()
    {
        if (self::$IS_CONNECTION === true)
        {
            return;
        }

        try
        {
            $dsn = DSN::getInstance()->loadConfigures(Configure::$CONFIGURES);
            self::$HANDLER = new PDO(
                $dsn->toString(),
                $dsn->username,
                $dsn->password,
                [
                    PDO::ATTR_PERSISTENT => true
                ]
            );
            self::$IS_CONNECTION = true;
        }
        catch (PDOException $e)
        {
            self::$HANDLER = null;
            throw new SqlmapException($e->getMessage(), $e->getCode());
        }
    }



    /**
     * disconnect db disconnection
     */
    public static function disconnect()
    {
        if (self::$IS_CONNECTION === true)
        {
            self::$HANDLER = null;
            self::$IS_CONNECTION = false;
        }
    }



    /**
     * begin transaction
     *
     * @throws SqlmapException
     */
    public static function begin()
    {
        if (self::$IS_TRANSACTIONS === false && self::callHandler()->beginTransaction() === true)
        {
            self::$IS_TRANSACTIONS = true;
        }
    }



    /**
     * commit transaction
     *
     * @throws SqlmapException
     */
    public static function commit()
    {
        if (self::$IS_TRANSACTIONS === true && self::callHandler()->commit() === true)
        {
            self::$IS_TRANSACTIONS = false;
        }
    }


    /**
     * commit transaction
     *
     * @throws SqlmapException
     */
    public static function rollback()
    {
        if (self::$IS_TRANSACTIONS === true && self::callHandler()->rollBack() === true)
        {
            self::$IS_TRANSACTIONS = false;
        }
    }



    /**
     * select query executor
     *
     * @param Statement $statement
     * @param array|null            $parameters
     * @return Column[]
     * @throws SqlmapException
     */
    public static function select(Statement $statement, array $parameters = null): array
    {
        // 結果クラス
        $instance = self::generateResultInstance($statement);

        // クエリ実行
        try
        {
            // prepare cache
            $sth = self::prepareStatementCache($statement);

            // execute
            if ($sth->execute($parameters) === false)
            {
                $errorInfo = $sth->errorInfo();
                throw new SqlmapException($errorInfo[0].':'.$errorInfo[2], 0);
            }

            // method exist
            $is_method_exist_bint_column = method_exists($instance, 'bindColumn');

            // ganerate_flags
            $generate_flags = [];

            // result
            $result = [];
            while ($row = $sth->fetch(PDO::FETCH_ASSOC))
            {
                $copyEntity = clone $instance;
                foreach ($row as $ky => $vl)
                {
                    if (isset($generate_flags[$ky]) === false)
                    {
                        $generate_flags[$ky] = strpos($ky, '.');
                    }

                    if ($generate_flags[$ky] !== false)
                    {
                        $kyAry = explode('.', $ky);
                        $generate_method = 'generate'.ucfirst($kyAry[0]);
                        $copyEntity->$generate_method();
                        $copyEntity->{$kyAry[0]}->{$kyAry[1]} = $vl;
                    }
                    else
                    {
                        $copyEntity->$ky = $vl;
                    }
                }
                // カラム補完
                if ($is_method_exist_bint_column === true)
                {
                    $copyEntity->bindColumn();
                }

                $result[] = $copyEntity;
            }

            // release
            return $result;
        }
        catch (PDOException $e)
        {
            throw SqlmapException::pdoErrorInfo($sth->errorInfo());
        }
    }



    /**
     * insert query executor
     *
     * @param Statement $statement
     * @param array|null            $parameters
     * @return int
     * @throws SqlmapException
     */
    public static function insert(Statement $statement, array $parameters = null): int
    {
        // クエリ実行
        try
        {
            // prepare cache
            $sth = self::prepareStatementCache($statement);

            // execute
            $result = $sth->execute($parameters);

            if ($result === false)
            {
                self::rollback();
                throw SqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $sth->rowCount();
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw SqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (SqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
    }



    /**
     * update query executor
     *
     * @param Statement $statement
     * @param array|null            $parameters
     * @return int
     * @throws SqlmapException
     */
    public static function update(Statement $statement, array $parameters = null): int
    {
        // クエリ実行
        try
        {
            // prepare cache
            $sth = self::prepareStatementCache($statement);

            // execute
            $result = $sth->execute($parameters);

            if ($result === false)
            {
                self::rollback();
                throw SqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $sth->rowCount();
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw SqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (SqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
    }



    /**
     * delete query executor
     *
     * @param Statement $statement
     * @param array|null            $parameters
     * @return int
     * @throws SqlmapException
     */
    public static function delete(Statement $statement, array $parameters = null): int
    {
        // クエリ実行
        try
        {
            // 削除の条件がない場合は、削除をしない。(全件削除回避のため。)
            if (empty($parameters) === true)
            {
                throw new SqlmapException('削除条件が足りません、削除要求をキャンセルしました。', 0);
            }

            // prepare cache
            $sth = self::prepareStatementCache($statement);

            // execute
            $result = $sth->execute($parameters);

            if ($result === false)
            {
                self::rollback();
                throw SqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $sth->rowCount();
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw SqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (SqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
    }



    /**
     * statement query executor
     *
     * @param Statement $statement
     * @param array|null            $parameters
     * @return bool
     * @throws SqlmapException
     */
    public static function statement(Statement $statement, array $parameters = null): bool
    {
        // クエリ実行
        try
        {
            // prepare
            $sth = self::callHandler()->prepare($statement->query);

            // execute
            $result = $sth->execute($parameters);

            if ($result === false)
            {
                self::rollback();
                throw SqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $result;
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw SqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (SqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
    }



    /**
     * call & generate handler
     *
     * @return PDO
     * @throws SqlmapException
     */
    public static function callHandler()
    {
        if (self::$HANDLER === null)
        {
            self::connect();
        }
        return self::$HANDLER;
    }



    /**
     * resultClass のインスタンスを取得
     *
     * @param Statement $statement ステートメント
     * @return Result
     */
    private static function generateResultInstance(Statement $statement)
    {
        $resultClass = $statement->result_class;
        if (is_null($resultClass) === false && class_exists($resultClass) === true)
        {
            return new $resultClass();
        }
        return new Result();
    }


    /**
     * prepare statement cache
     *
     * @param Statement $statement ステートメント
     * @return PDOStatement|null
     * @throws SqlmapException
     */
    private static function prepareStatementCache(Statement $statement)
    {
        $sth = null;
        if (empty(self::$_prepareQueries) === false)
        {
            $ky = array_search($statement->query, self::$_prepareQueries);
            if ($ky !== false)
            {
                $sth = self::$_prepareStatements[$ky];
            }
        }
        if (empty($sth) === true)
        {
            $sth = self::callHandler()->prepare($statement->query);
            self::$_prepareQueries[] = $statement->query;
            self::$_prepareStatements[] = $sth;
        }
        return $sth;
    }
}
