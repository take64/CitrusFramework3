<?php
/**
 * Xml.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  .
 * @license     http://www.besidesplus.net/
 */

namespace Citrus;


use DOMNamedNodeMap;

class CitrusXml
{
    /**
     * get named item value
     * $attribute 要素の $key 指定値を取得する。
     *
     * @param DOMNamedNodeMap $attributes
     * @param string $key
     * @return string
     */
    public static function getNamedItemValue(DOMNamedNodeMap $attributes, string $key) : string
    {
        $item = $attributes->getNamedItem($key);
        $value = null;
        if(isset($item) === true)
        {
            $value = $item->value;
        }
        return $value;
    }
}