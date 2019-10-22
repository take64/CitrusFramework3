<?php

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Migration;

use Citrus\Command\Console;
use Citrus\Database\DSN;
use PDO;

abstract class Item
{
    use Console;

    /** @var string object name */
    public $object_name = '';

    /** @var DSN data source name */
    protected $dsn;



    /**
     * migration.sh up
     */
    public abstract function up();



    /**
     * migration.sh down
     */
    public abstract function down();



    /**
     * constructor.
     *
     * @param DSN $dsn DSN情報
     */
    public function __construct(DSN $dsn)
    {
        $this->dsn = $dsn;
    }



    /**
     * query execute
     *
     * @param string $query query string
     */
    public function execute($query)
    {
        // 呼びもと
        $calling_func = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        $this->format(
            '%s executing %s!', get_class($this), $calling_func
        );

        $query = str_replace('{SCHEMA}', $this->dsn->schema . '.', $query);

        // 実行開始タイム
        $timestamp = microtime(true);

        $db = new PDO($this->dsn->toStringWithAuth());
        $db->exec($query);

        // 実行終了時間
        $execute_microsecond = microtime(true) - $timestamp;

        // エラー確認
        $compare_not_error = [ '00000', null, null ];
        $error_info = $db->errorInfo();
        // 正常実行
        if ($error_info == $compare_not_error)
        {
            $this->success(
                sprintf('%s executed. %f μs', get_class($this), $execute_microsecond)
            );
        }
        // 異常実行
        else if (is_null($error_info) === false)
        {
            $this->error(
                sprintf('%s has error.', get_class($this)) . PHP_EOL .
                sprintf('    %s %s %s', $error_info[0], $error_info[1], $error_info[2])
            );
        }
    }
}
