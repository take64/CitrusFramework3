<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;


use Citrus\CitrusObject;

class CitrusLoggerFile extends CitrusObject implements CitrusLoggerType
{
    /** @var string */
    public $directory;

    /** @var string */
    public $filename;



    /**
     * constructor
     *
     * @param array $configure
     */
    public function __construct(array $configure = [])
    {
        $this->bind($configure);
    }



    /**
     * output log file
     *
     * @param mixed $value
     * @param array $params
     */
    public function output($value, array $params = [])
    {
        $_directory = $this->directory;
        $_filename  = $this->filename;
        $logfile    = sprintf('%s%s-%s', $_directory, $_filename, date('Ymd', $_SERVER['REQUEST_TIME']);

        $vl_dump = '';
        if (is_string($value) === true)
        {
            $vl_dump = vsprintf($value, $params) . PHP_EOL;
        }
        else
        {
            ob_start();
            var_dump($value);
            $vl_dump = ob_get_contents();
            ob_end_clean();
        }

        $dat = date('[Y-m-d H:i:s] ', $_SERVER['REQUEST_TIME']).htmlspecialchars_decode(strip_tags($vl_dump));

        // log file exist
        $file_exist = file_exists($logfile);

        // writing log
        $fp = @fopen($logfile, 'a+');
        if ($fp === false && mkdir(dirname($logfile)) === true)
        {
            $fp = fopen($logfile, 'a+');
        }
        fwrite($fp, $dat);
        fclose($fp);

        // file added permission
        if ($file_exist === false)
        {
            chmod($logfile, 0666);
            chown($logfile, 'wwwrun');
            chgrp($logfile, 'www');
        }

        clearstatcache();
    }
}
