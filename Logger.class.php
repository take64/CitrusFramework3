<?php
/**
 * Logger.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  .
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Logger\CitrusLoggerFile;
use Citrus\Logger\CitrusLoggerSyslog;
use Citrus\Logger\CitrusLoggerType;

class CitrusLogger
{
    /**
     * logger type file
     *
     * @var string
     */
    const LOG_TYPE_FILE = 'file';

    /**
     * logger type database.sh
     *
     * @var string
     */
//    const LOG_TYPE_DATABASE = 'database.sh';

    /**
     * logger type smtp mail
     *
     * @var string
     */
//    const LOG_TYPE_SMTP = 'smtp';

    /**
     * logger type php syslog
     *
     * @var string
     */
    const LOG_TYPE_SYSLOG = 'syslog';

    /** @var int log level */
    const LOG_LEVEL_TRACE   = 0;

    /** @var int log level */
    const LOG_LEVEL_DEBUG   = 1;

    /** @var int log level */
    const LOG_LEVEL_INFO    = 2;

    /** @var int log level */
    const LOG_LEVEL_WARN    = 3;

    /** @var int log level */
    const LOG_LEVEL_ERROR   = 4;

    /** @var int log level */
    const LOG_LEVEL_FATAL   = 5;

    /** @var int log level */
    public static $LOG_LEVEL = 0;

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
     * @param array $configure
     * @return CitrusLoggerType
     */
    public static function initialize($default_configure = [], $configure = []) : CitrusLoggerType
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return self::$INSTANCE;
        }

        // configure
        $logger = [];
        $logger = array_merge($logger, CitrusNVL::ArrayVL($default_configure, 'logger', []));
        $logger = array_merge($logger, CitrusNVL::ArrayVL($configure, 'logger', []));

// var_dump($logger, $default_configure, $configure);
        // log type select
        $type = $logger['type'];
//        if (isset($default_configure['logger']) === true && isset($default_configure['logger']['type']) === true)
//        {
//            $type = $default_configure['logger']['type'];
//        }
//        if (isset($configure['logger']) === true && isset($configure['logger']['type']) === true)
//        {
//            $type = $configure['logger']['type'];
//        }

        // log level
//        $level = 'debug';
        $level = $logger['level'];
        if (empty($level) === true)
        {
            $level = 'debug';
        }
//        if (isset($default_configure['logger']) === true && isset($default_configure['logger']['level']) === true)
//        {
//            $level = $default_configure['logger']['level'];
//        }
//        if (isset($configure['logger']) === true && isset($configure['logger']['level']) === true)
//        {
//            $level = $configure['logger']['level'];
//        }
        switch ($level)
        {
            case 'trace' : self::$LOG_LEVEL = self::LOG_LEVEL_TRACE; break; // trace
            case 'debug' : self::$LOG_LEVEL = self::LOG_LEVEL_DEBUG; break; // debug
            case 'info'  : self::$LOG_LEVEL = self::LOG_LEVEL_INFO;  break; // info
            case 'warn'  : self::$LOG_LEVEL = self::LOG_LEVEL_WARN;  break; // warn
            case 'error' : self::$LOG_LEVEL = self::LOG_LEVEL_ERROR; break; // error
            case 'fatal' : self::$LOG_LEVEL = self::LOG_LEVEL_FATAL; break; // fatal
            case 'trace' : self::$LOG_LEVEL = self::LOG_LEVEL_TRACE; break; // trace
        }

        // logger instance
        switch ($type)
        {
            // file
            case self::LOG_TYPE_FILE :
                self::$INSTANCE = new CitrusLoggerFile($logger);
                break;
            // syslog
            case self::LOG_TYPE_SYSLOG :
                self::$INSTANCE = new CitrusLoggerSyslog($logger);
                break;
        }

        // display
// var_dump($logger);
// var_dump(CitrusConfigure::$CONFIGURE_ITEM);
        self::$LOG_DISPLAY = CitrusNVL::NVL($logger['display'], false);


        // initialized
        self::$IS_INITIALIZED = true;

        return self::$INSTANCE;
    }



    /**
     * trace log file
     *
     * @param mixed $value
     * @param array ...$_
     */
    public static function trace($value, ...$_)
    {
        self::_output($value, func_get_args());
    }



    /**
     * debug log file
     *
     * @param mixed $value
     * @param array ...$_
     */
    public static function debug($value, ...$_)
    {
        if (self::$LOG_LEVEL <= self::LOG_LEVEL_DEBUG)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * info log file
     *
     * @param mixed $value
     * @param array ...$_
     */
    public static function info($value, ...$_)
    {
        if (self::$LOG_LEVEL <= self::LOG_LEVEL_INFO)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * warn log file
     *
     * @param mixed $value
     * @param array ...$_
     */
    public static function warn($value, ...$_)
    {
        if (self::$LOG_LEVEL <= self::LOG_LEVEL_WARN)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * error log file
     *
     * @param mixed $value
     * @param array ...$_
     */
    public static function error($value, ...$_)
    {
        if (self::$LOG_LEVEL <= self::LOG_LEVEL_ERROR)
        {
            self::_output($value, func_get_args());
        }
    }



    /**
     * fatal log file
     *
     * @param mixed $value
     * @param array ...$_
     */
    public static function fatal($value, ...$_)
    {
        if (self::$LOG_LEVEL <= self::LOG_LEVEL_FATAL)
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
var_dump(self::$LOG_DISPLAY);
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
        self::$INSTANCE->output($value, $params);
    }
}
