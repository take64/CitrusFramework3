<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

trait Singleton
{
    /**
     * call singleton instance
     */
    private static function callSingleton()
    {
        static $singleton;
        if (is_null($singleton) === true)
        {
            $singleton = new static();
        }
        return $singleton;
    }
}