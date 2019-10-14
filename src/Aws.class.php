<?php
/**
 * @copyright   Copyright 2018, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

class Aws
{
    /** CitrusConfigureキー */
    const CONFIGURE_KEY = 'aws';



    /** @var bool */
    private static $IS_INITIALIZED = false;



    /**
     * initialize
     */
    public static function initialize()
    {
        // is initialize
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // configure
        $configure = Configure::configureMerge(self::CONFIGURE_KEY);

        // require
        require_once $configure['path'];

        // initialize
        self::$IS_INITIALIZED = true;
    }
}
