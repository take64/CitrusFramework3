<?php
/**
 * Syslog.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Logger
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;


use Citrus\CitrusObject;

class CitrusLoggerSyslog extends CitrusObject implements CitrusLoggerType
{
    /** @var string */
    public $directory;

    /** @var string */
    public $filename;



    /**
     * constructor
     *
     * @param array $default_configure
     * @param array $configure
     */
    public function __construct($default_configure = [], $configure = [])
    {
    }

    /**
     * output log syslog
     *
     * @param mixed  $value
     * @param string $comment
     */
    public function output($value, string $comment = '')
    {
        $vl_dump = '';
        if (is_string($value))
        {
            $vl_dump = $value . "\n";
        }
        else
        {
            ob_start();
            var_dump($value);
            $vl_dump = ob_get_contents();
            ob_end_clean();
        }

        $dat = date('[Y-m-d H:i:s]',$_SERVER['REQUEST_TIME']).$comment.htmlspecialchars_decode(strip_tags($vl_dump));

        syslog(LOG_INFO, $dat);
    }
}
