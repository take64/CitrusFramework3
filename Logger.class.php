<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Logger\CitrusLoggerFile;
use Citrus\Logger\CitrusLoggerLevel;
use Citrus\Logger\CitrusLoggerSyslog;
use Citrus\Logger\CitrusLoggerType;

class CitrusLogger
{
    /** @var string logger type file */
    const LOG_TYPE_FILE = 'file';

    /** @var string logger type php syslog */
    const LOG_TYPE_SYSLOG = 'syslog';

    /** @var string CitrusConfigureキー */
    const CONFIGURE_KEY = 'logger';



    /** @var string log level */
    public static $LOG_LEVEL = CitrusLoggerLevel::DEBUG;

    /** @var bool log display */
    public static $LOG_DISPLAY = false;

    /** @var CitrusLoggerType */
    protected static $INSTANCE = null;

    /** @var bool is initialized */
    public static $IS_INITIALIZED = false;



    /**
     * initialize logger
     *
     * @param array $default_configure
     * @param array $configure_domain
     * @return CitrusLoggerType
     */
    public static function initialize($default_configure = [], $configure_domain = []) : CitrusLoggerType
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return self::$INSTANCE;
        }

        // configure
        $configure = CitrusConfigure::configureMerge(self::CONFIGURE_KEY, $default_configure, $configure_domain);

        // log type select
        $type = $configure['type'];

        // log level
        $level = CitrusNVL::ArrayVL($configure, 'level', CitrusLoggerLevel::DEBUG);
        if (in_array($level, CitrusLoggerLevel::$LEVELS, true) === true)
        {
            self::$LOG_LEVEL = $level;
        }

        // logger instance
        switch ($type)
        {
            // file
            case self::LOG_TYPE_FILE :
                self::$INSTANCE = new CitrusLoggerFile($configure);
                break;
            // syslog
            case self::LOG_TYPE_SYSLOG :
                self::$INSTANCE = new CitrusLoggerSyslog($configure);
                break;
            default:
        }

        // display
        self::$LOG_DISPLAY = CitrusNVL::NVL($configure['display'], false);

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
        self::_output($value, func_get_args());
    }



    /**
     * debug log file
     *
     * @param mixed $value
     */
    public static function debug($value)
    {
        if (self::isOutputLevel(CitrusLoggerLevel::DEBUG) === true)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * info log file
     *
     * @param mixed $value
     */
    public static function info($value)
    {
        if (self::isOutputLevel(CitrusLoggerLevel::INFO) === true)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * warn log file
     *
     * @param mixed $value
     */
    public static function warn($value)
    {
        if (self::isOutputLevel(CitrusLoggerLevel::WARNING) === true)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * error log file
     *
     * @param mixed $value
     */
    public static function error($value)
    {
        if (self::isOutputLevel(CitrusLoggerLevel::ERROR) === true)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * fatal log file
     *
     * @param mixed $value
     */
    public static function fatal($value)
    {
        if (self::isOutputLevel(CitrusLoggerLevel::FATAL) === true)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * output log file
     *
     * @param mixed  $value
     * @param array  $params
     */
    private static function _output($value, array $params)
    {
        // params
        array_shift($params);

        // display
        if (self::$LOG_DISPLAY === true)
        {
            $display_value = $value;
            if (is_string($value) === true)
            {
                $display_value = vsprintf($value, $params);

            }
            var_dump([
                Citrus::$TIMESTAMP_FORMAT,
                $display_value,
            ]);

        }
        if (is_null(self::$INSTANCE) === false)
        {
            self::$INSTANCE->output($value, $params);
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
        $configure_level_index = array_search(self::$LOG_LEVEL, CitrusLoggerLevel::$LEVELS, true);
        $target_level_index = array_search($level, CitrusLoggerLevel::$LEVELS, true);

        return ($configure_level_index <= $target_level_index);
    }
}
