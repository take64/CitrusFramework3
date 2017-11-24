<?php
/**
 * Exception.class.php.
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


use Exception;
use Throwable;

class CitrusException extends Exception
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

        CitrusLogger::error($this);
    }



    /**
     * CitrusException converter
     *
     * @param Exception $e
     * @return CitrusException
     */
    public static function convert(Exception $e)
    {
        return new static($e->getMessage(), $e->getCode(), $e->getPrevious());
    }
}