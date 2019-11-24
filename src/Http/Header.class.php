<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Http;

class Header
{
    /**
     * status code 404
     * 404 Not Found を返す
     */
    public static function status404()
    {
        header("HTTP/1.0 404 Not Found");
    }
}
