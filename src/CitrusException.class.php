<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Exception;
use Throwable;

/**
 * Citrusの基本例外
 */
class CitrusException extends Exception
{
    /**
     * constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        Logger::error($this);
    }



    /**
     * CitrusException converter
     *
     * @param Exception $e
     * @return self
     */
    public static function convert(\Exception $e)
    {
        return new self($e->getMessage(), $e->getCode(), $e->getPrevious());
    }
}
