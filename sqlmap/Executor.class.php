<?php
/**
 * Executor.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Sqlmap
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Sqlmap;


use Citrus\CitrusConfigure;
use Citrus\Database\CitrusDatabaseColumn;
use PDO;
use PDOException;

class CitrusSqlmapExecutor
{
    /** @var PDO db connection handler */
    public static $HANDLER = null;

    /** @var bool transaction flg */
    public static $IS_TRANSACTIONS = false;

    /** @var bool db connection flg */
    private static $IS_CONNECTION = false;

    /** @var array db prepare cache */
    private static $_prepareQueries = array();

    /** @var array db prepare cache */
    private static $_prepareStatements = array();



    /**
     * connect db connection
     *
     * @access  public
     * @since   0.0.1.0 2012.02.06
     * @version 0.0.1.0 2012.02.06
     */
    public static function connect()
    {
        if (self::$IS_CONNECTION === true)
        {
            return ;
        }

        try
        {
            $dsn = CitrusConfigure::$CONFIGURE_ITEM->database;
            self::$HANDLER = new PDO(
                $dsn->toString(),
                $dsn->username,
                $dsn->password,
                array(
                    PDO::ATTR_PERSISTENT => true
                )
            );
            self::$IS_CONNECTION = true;
        }
        catch (PDOException $e)
        {
            self::$HANDLER = null;
            throw new CitrusSqlmapException($e->getMessage(), $e->getCode());
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
     */
    public static function rollback()
    {
        if (self::$IS_TRANSACTIONS === true && self::callHandler()->rollBack() === true)
        {
            self::$IS_TRANSACTIONS = false;
        }
    }



    /**
     * transaction validate
     *
     * @param CitrusSqlmapStatement     $statement
     * @param CitrusDatabaseColumn|null $parameter
     * @return bool
     */
    private static function _validate(CitrusSqlmapStatement $statement, CitrusDatabaseColumn $parameter = null) : bool
    {
        $query = $statement->query;

        // クエリ文字列に':'に含まれていない場合は検査の必要なし
        $startPos = strrpos($query, ':');
        if ($startPos === false)
        {
            return true;
        }
        $array = explode(substr($query, $startPos), ' ');
        foreach($array as $one)
        {
            if (strrpos($one, ':') === 0)
            {
                if (property_exists($parameter, substr($one, 1)) === true)
                {
                    return true;
                }
            }
        }
        return false;
    }



    /**
     * select query executor
     *
     * @param CitrusSqlmapStatement $statement
     * @param array|null            $parameter_list
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public static function select(CitrusSqlmapStatement $statement, array $parameter_list = null) : array
    {
        // 結果クラス
        $instance = null;
        $resultClass = $statement->result_class;
        if (is_null($resultClass) === false && class_exists($resultClass) === true)
        {
            $instance = new $resultClass();
        }
        else
        {
            $instance = new CitrusDatabaseColumn();
        }
        self::_validate($statement, $parameter_list);

        // クエリ実行
        try
        {
            // prepare cache
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

            // execute
            if ($sth->execute($parameter_list) === false)
            {
                $errorInfo = $sth->errorInfo();
                throw new CitrusSqlmapException($errorInfo[0].':'.$errorInfo[2], 0);
            }

            // method exist
            $is_method_exist_bintColumn = method_exists($instance, 'bindColumn');

            // ganerate_flags
            $generate_flags = array();

            // result
            $result = array();
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
                if ($is_method_exist_bintColumn === true)
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
            throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
        }
    }



    /**
     * insert query executor
     *
     * @param CitrusSqlmapStatement $statement
     * @param array|null            $parameter_list
     * @return bool
     * @throws CitrusSqlmapException
     */
    public static function insert(CitrusSqlmapStatement $statement, array $parameter_list = null) : bool
    {
        // クエリ実行
        try
        {
            // prepare cache
            $sth = null;
            if (empty(self::$_prepareQueries) === false)
            {
                $ky = array_search($statement->query, self::$_prepareQueries);
                if ($ky !== false)
                {
                    $sth = self::$_prepareStatements[$ky];
                }
            }
            if (is_null($sth) === true)
            {
                $sth = self::callHandler()->prepare($statement->query);
                self::$_prepareQueries[] = $statement->query;
                self::$_prepareStatements[] = $sth;
            }

            // execute
            $result = $sth->execute($parameter_list);

            if ($result === false)
            {
                self::rollback();
                throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $result;
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (CitrusSqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
        return false;
    }



    /**
     * update query executor
     *
     * @param CitrusSqlmapStatement $statement
     * @param array|null            $parameter_list
     * @return bool
     * @throws CitrusSqlmapException
     */
    public static function update(CitrusSqlmapStatement $statement, array $parameter_list = null) : bool
    {
        // クエリ実行
        try
        {
            // prepare cache
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

            // execute
            $result = $sth->execute($parameter_list);

            if ($result === false)
            {
                self::rollback();
                throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $result;
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (CitrusSqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
        return false;
    }



    /**
     * delete query executor
     *
     * @param CitrusSqlmapStatement $statement
     * @param array|null            $parameter_list
     * @return bool
     * @throws CitrusSqlmapException
     */
    public static function delete(CitrusSqlmapStatement $statement, array $parameter_list = null) : bool
    {
        // クエリ実行
        try
        {
            // 削除の条件がない場合は、削除をしない。(全件削除回避のため。)
            if (empty($parameter_list) === true)
            {
                throw new CitrusSqlmapException('削除条件が足りません、削除要求をキャンセルしました。', 0, __FILE__, __LINE__);
            }

            // prepare cache
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

            $result = $sth->execute($parameter_list);

            if ($result === false)
            {
                self::rollback();
                throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $result;
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (CitrusSqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
        return false;
    }



    /**
     * statement query executor
     *
     * @param CitrusSqlmapStatement $statement
     * @param array|null            $parameter_list
     * @return bool
     * @throws CitrusSqlmapException
     */
    public static function statement(CitrusSqlmapStatement $statement, array $parameter_list = null) : bool
    {
        // クエリ実行
        try
        {
            // prepare
            $sth = self::callHandler()->prepare($statement->query);

            $result = $sth->execute($parameter_list);

            if ($result === false)
            {
                self::rollback();
                throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
            }

            return $result;
        }
        catch (PDOException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw CitrusSqlmapException::pdoErrorInfo($sth->errorInfo());
        }
        catch (CitrusSqlmapException $e)
        {
            self::$IS_TRANSACTIONS = false;
            throw $e;
        }
        return false;
    }



    /**
     * call & generate handler
     *
     * @return PDO
     */
    public static function callHandler()
    {
        if (self::$HANDLER === null)
        {
            self::connect();
        }
        return self::$HANDLER;
    }
}