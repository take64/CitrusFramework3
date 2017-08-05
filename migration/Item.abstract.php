<?php
/**
 * Item.abstract.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Migration
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Migration;


use Citrus\Database\CitrusDatabaseDSN;
use PDO;

abstract class CitrusMigrationItem
{
    /** @var string object name */
    public $object_name = '';

    /** @var CitrusDatabaseDSN data source name */
    public $dsn = null;

    /**
     * migration.sh up
     */
    public abstract function up();

    /**
     * migration.sh down
     */
    public abstract function down();

    /**
     * constructor
     *
     * @param CitrusDatabaseDSN $dsn
     */
    public function __construct(CitrusDatabaseDSN $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * query execute
     *
     * @param $query query string
     */
    public function execute($query)
    {
        $query = str_replace('{SCHEMA}', $this->dsn->schema . '.', $query);

        // 実行開始タイム
        $timestamp = microtime(true);

        $db = new PDO($this->dsn->toStringWithAuth());
        $count = $db->exec($query);

        // 実行終了時間
        $execute_microsecond = microtime(true) - $timestamp;

        // エラー確認
        $compare_not_error = [ '00000', null, null ];
        $error_info = $db->errorInfo();
        // 正常実行
        if ($error_info == $compare_not_error)
        {
            echo get_class($this) . ' executed ' . $execute_microsecond . 'µs' . PHP_EOL;
        }
        // 異常実行
        else if (is_null($error_info) === false)
        {
            echo get_class($this) . ' has error.' . PHP_EOL;
            echo sprintf('    %s %s %s' . PHP_EOL,
                $error_info[0],
                $error_info[1],
                $error_info[2]
                );
        }

    }
}