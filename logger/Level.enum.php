<?php
/**
 * @copyright   Copyright 2018, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;


class CitrusLoggerLevel
{
    /** trace */
    const TRACE = 'trace';

    /** debug */
    const DEBUG = 'debug';

    /** info */
    const INFO = 'info';

    /** notice */
    const NOTICE = 'notice';

    /** warning */
    const WARNING = 'warning';

    /** error */
    const ERROR = 'error';

    /** critical */
    const CRITICAL = 'critical';

    /** alert */
    const ALERT = 'alert';

    /** fatal */
    const FATAL = 'fatal';

    /** @var string[]  */
    public static $LEVELS = [
        self::TRACE,
        self::DEBUG,
        self::INFO,
        self::NOTICE,
        self::WARNING,
        self::ERROR,
        self::CRITICAL,
        self::ALERT,
        self::FATAL,
    ];
}