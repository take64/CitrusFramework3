<?php
/**
 * Singleton.trait.php.
 * 2017/09/17
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


trait CitrusSingleton
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