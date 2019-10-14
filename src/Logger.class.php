<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Logger\Cloudwatch;
use Citrus\Logger\File;
use Citrus\Logger\Level;
use Citrus\Logger\Syslog;
use Citrus\Logger\LogType;

class Logger
{
    /** logger type file */
    const LOG_TYPE_FILE = 'file';

    /** logger type php syslog */
    const LOG_TYPE_SYSLOG = 'syslog';

    /** logger type cloudwatch */
    const LOG_TYPE_CLOUDWATCH = 'cloudwatch';

    /** CitrusConfigureキー */
    const CONFIGURE_KEY = 'logger';



    /** @var string log level */
    public static $LOG_LEVEL = Level::DEBUG;

    /** @var bool log display */
    public static $LOG_DISPLAY = false;

    /** @var LogType */
    protected static $INSTANCE = null;

    /** @var bool is initialized */
    public static $IS_INITIALIZED = false;



    /**
     * initialize logger
     *
     * @param array $default_configure
     * @param array $configure_domain
     * @return LogType
     */
    public static function initialize($default_configure = [], $configure_domain = []) : LogType
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return self::$INSTANCE;
        }

        // configure
        $configure = Configure::configureMerge(self::CONFIGURE_KEY, $default_configure, $configure_domain);

        // log type select
        $type = $configure['type'];

        // log level
        $level = NVL::ArrayVL($configure, 'level', Level::DEBUG);
        if (in_array($level, Level::$LEVELS, true) === true)
        {
            self::$LOG_LEVEL = $level;
        }

        // logger instance
        switch ($type)
        {
            // file
            case self::LOG_TYPE_FILE :
                self::$INSTANCE = new File($configure);
                break;
            // syslog
            case self::LOG_TYPE_SYSLOG :
                self::$INSTANCE = new Syslog($configure);
                break;
            // syslog
            case self::LOG_TYPE_CLOUDWATCH :
                self::$INSTANCE = new Cloudwatch($configure);
                break;
            default:
        }

        // display
        self::$LOG_DISPLAY = NVL::NVL($configure['display'], false);

        // initialized
        self::$IS_INITIALIZED = true;

        return self::$INSTANCE;
    }



    /**
     * trace log file
     *
     * @param mixed $value
     */
    public static function trace($value)
    {
        self::output(Level::TRACE, $value, func_get_args());
    }



    /**
     * debug log file
     *
     * @param mixed $value
     */
    public static function debug($value)
    {
        self::output(Level::DEBUG, $value, func_get_args());
    }



    /**
     * info log file
     *
     * @param mixed $value
     */
    public static function info($value)
    {
        self::output(Level::INFO, $value, func_get_args());
    }



    /**
     * warn log file
     *
     * @param mixed $value
     */
    public static function warn($value)
    {
        self::output(Level::WARNING, $value, func_get_args());
    }



    /**
     * error log file
     *
     * @param mixed $value
     */
    public static function error($value)
    {
        self::output(Level::ERROR, $value, func_get_args());
    }



    /**
     * fatal log file
     *
     * @param mixed $value
     */
    public static function fatal($value)
    {
        self::output(Level::FATAL, $value, func_get_args());
    }



    /**
     * output log file
     *
     * @param string $level  ログレベル
     * @param mixed  $value  ログの内容
     * @param array  $params パラメータ
     */
    public static function output(string $level, $value, array $params)
    {
        // ログレベルによる出力許容チェック
        if (false === self::isOutputLevel($level))
        {
            return ;
        }

        // params
        array_shift($params);

        // display
        if (true === self::$LOG_DISPLAY)
        {
            $display_value = $value;
            if (true === is_string($value))
            {
                $display_value = vsprintf($value, $params);

            }
            var_dump([
                Citrus::$TIMESTAMP_FORMAT,
                $display_value,
            ]);

        }
        if (false === is_null(self::$INSTANCE))
        {
            self::$INSTANCE->output($level, $value, $params);
        }
    }



    /**
     * コンフィグ設定で指定されたログを出力するレベルか判定する
     *
     * @param string $level
     * @return bool
     */
    private static function isOutputLevel(string $level)
    {
        $configure_level_index = array_search(self::$LOG_LEVEL, Level::$LEVELS, true);
        $target_level_index = array_search($level, Level::$LEVELS, true);

        return ($configure_level_index <= $target_level_index);
    }
}
