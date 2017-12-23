<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Http;


class CitrusHttpHeader
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