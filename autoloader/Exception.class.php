<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Autoloader;


use Citrus\CitrusLogger;
use Exception;
use Throwable;

class CitrusAutoloaderException extends Exception
{
    /**
     * constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        CitrusLogger::error($message);
    }



    /**
     * CitrusException converter
     *
     * @param Exception $e
     * @return CitrusAutoloaderException
     */
    public static function convert(Exception $e)
    {
        return new static($e->getMessage(), $e->getCode(), $e->getPrevious());
    }
}