<?php
/**
 * Type.interface.php.
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


interface CitrusLoggerType
{
    /**
     * output
     *
     * @param mixed  $value
     * @param string $comment
     */
    public function output($value, string $comment = '');
}