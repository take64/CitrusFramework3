<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use DOMNamedNodeMap;

class Xml
{
    /**
     * get named item value
     * $attribute 要素の $key 指定値を取得する。
     *
     * @param DOMNamedNodeMap $attributes
     * @param string $key
     * @return string|null
     */
    public static function getNamedItemValue(DOMNamedNodeMap $attributes, string $key)
    {
        $item = $attributes->getNamedItem($key);
        $value = null;
        if (isset($item) === true)
        {
            $value = $item->value;
        }
        return $value;
    }



    /**
     * DOMNamedNodeMap to list
     * $attribute 要素の $key => $value で取得する。
     *
     * @param DOMNamedNodeMap $attributes
     * @return array
     */
    public static function toList(DOMNamedNodeMap $attributes)
    {
        $items = [];
        foreach ($attributes as $name => $attribute)
        {
            $items[$name] = $attribute->value;
        }
        return $items;
    }
}