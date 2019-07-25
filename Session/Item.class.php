<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Session;

use Citrus\Struct;

class Item extends Struct
{
    /**
     * constructor.
     *
     * @param Item|null $session
     */
    public function __construct($session = null)
    {
        // is null
        if (is_null($session) === true)
        {
            return ;
        }

        if ($session instanceof Item)
        {
            $this->bind($session->properties());
        }
        else if (is_array($session) === true)
        {
            foreach ($session as $ky => $vl)
            {
                $this->$ky = serialize($vl);
            }
        }
    }



    /**
     * session value parse method
     *
     * @param Item $element
     */
    public function parseItem(Item $element)
    {
        $this->bindObject($element);
    }



    /**
     * session value regist method
     *
     * @param string $key
     * @param Struct $value
     */
    public function regist(string $key, $value)
    {
        $this->$key = serialize($value);
    }



    /**
     * session value call
     *
     * @param string $key
     * @return mixed|null
     */
    public function call(string $key)
    {
        if (isset($this->$key) === true)
        {
            return unserialize($this->$key);
        }
        return null;
    }



    /**
     * session value calls
     *
     * @return mixed[]
     */
    public function properties() : array
    {
        $result = [];
        $property_keys = array_keys(parent::properties());
        foreach ($property_keys as $one)
        {
            $result[$one] = $this->call($one);
        }
        return $result;
    }



    /**
     * general bind method
     *
     * @param array|null $array
     * @param bool       $strict
     */
    public function bind(array $array = null, bool $strict = false)
    {
        $this->bindArray($array, $strict);
    }



    /**
     * general bind array method
     *
     * @param array|null $array
     * @param bool       $strict
     */
    public function bindArray(array $array = null, bool $strict = false)
    {
        if (is_null($array) === true)
        {
            return ;
        }
        foreach ($array as $ky => $vl)
        {
            $this->set($ky, serialize($vl), $strict);
        }
    }



    /**
     * general bind object method
     *
     * @param mixed|null $object
     * @param bool       $strict
     */
    public function bindObject($object = null, $strict = false)
    {
        var_dump($object);
        if (is_null($object) === true)
        {
            return ;
        }
        $array = get_object_vars($object);
        $this->bindArray($array, $strict);
    }
}