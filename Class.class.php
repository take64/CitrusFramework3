<?php
/**
 * Class.class.php.
 *
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


class CitrusClass
{
    /**
     * class vars getter
     *
     * @return array
     */
    public function properties() : array
    {
        return get_class_vars($this);
    }



    /**
     * general getter method
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (isset(self::$key) === true)
        {
            return self::$key;
        }
        return null;
    }



    /**
     * general setter method
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        self::$key = $value;
    }



    /**
     * general adder method
     *
     * @param string $key
     * @param mixed  $value
     */
    public function add(string $key, $value)
    {
        $add = &self::$key;

        if ($add == null)
        {
            if (is_array($value) === true)
            {
                $add = $value;
            }
            else
            {
                $add = [$value];
            }
        }
        else if (is_array($add) === true)
        {
            if (is_array($value))
            {
                $add = $add + $value;
            }
            else
            {
                array_push($add, $value);
            }
        }
        else if (is_array($add) === false)
        {
            $add = [$add, $value];
        }
    }



    /**
     * general remover method
     *
     * @param array|string $key
     */
    public function remove($key)
    {
        if (is_array($key) === true)
        {
            foreach ($key as $ky => $vl)
            {
                self::$ky = null;
            }
        }
        else
        {
            self::$key = null;
        }
    }



    /**
     * general bind method
     *
     * @param array|null $array
     */
    public static function bind(array $array = null)
    {
        self::bindArray($array);
    }



    /**
     * general bind array method
     *
     * @param array|null $array
     */
    public static function bindArray(array $array = null)
    {
        if (is_null($array) === true)
        {
            return ;
        }
        foreach ($array as $ky => $vl)
        {
            self::$ky = $vl;
        }
    }



    /**
     * general bind object method
     *
     * @param mixed|null $object
     */
    public function bindObject($object = null)
    {
        if (is_null($object) === true)
        {
            return ;
        }
        $array = get_object_vars($object);
        foreach ($array as $ky => $vl)
        {
            self::$ky = $vl;
        }
    }
}