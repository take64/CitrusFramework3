<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

trait Singleton
{
    /**
     * call singleton instance
     *
     * @return self
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