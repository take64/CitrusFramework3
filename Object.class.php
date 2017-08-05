<?php
/**
 * Object.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus.
 * @subpackage  .
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


class CitrusObject
{
    /**
     * compare object
     *
     * @param mixed $object
     * @return bool
     */
    public function equals($object)
    {
        return ($this === $object);
    }



    /**
     * obeject vars getter
     *
     * @return array
     */
    public function properties() : array
    {
        return get_object_vars($this);
    }



    /**
     * obeject vars serialize getter
     *
     * @return string
     */
    public function serialize() : string
    {
        return serialize($this);
    }



    /**
     * class name getter
     *
     * @return string
     */
    public function getClass() : string
    {
        return get_class($this);
    }



    /**
     * instance clone getter method
     *
     * @return object
     */
    public function getClone()
    {
        return clone $this;
    }



    /**
     * general getter method
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->$key) === true)
        {
            return $this->$key;
        }
        return null;
    }



    /**
     * general setter method
     *
     * @param mixed $key
     * @param mixed $value
     * @param bool  $strict
     */
    public function set($key, $value, bool $strict = false)
    {
        if ($strict === true)
        {
            if (property_exists($this, $key) === true)
            {
                $this->$key = $value;
            }
        }
        else
        {
            $this->$key = $value;
        }
    }



    /**
     * general adder method
     *
     * @param string $key
     * @param mixed  $value
     */
    public function add($key, $value)
    {
        $add = &$this->$key;

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
                unset($this->$vl);
            }
        }
        else
        {
            unset($this->$key);
        }
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
            $this->set($ky, $vl, $strict);
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
        if (is_null($object) === true)
        {
            return ;
        }
        $array = get_object_vars($object);
        $this->bindArray($array, $strict);
    }



    /**
     * get value from context path
     *
     * @param string $context
     * @return mixed
     */
    public function getFromContext($context)
    {
        $context_list = explode('.', $context);
        $context_size = count($context_list);
        $context_get_limit = $context_size - 1;

        $object = $this;
        for ($i = 1; $i <= $context_get_limit; $i++)
        {
            $method = 'get';
            $method_properties = explode('_', $context_list[$i]);
            foreach ($method_properties as $ky => $vl)
            {
                $method .= ucfirst(strtolower($vl));
            }
            if (method_exists($object, $method) === true)
            {
                $object = $object->$method();
            }
            else
            {
                $object = $object->get($context_list[$i]);
            }
        }

        return $object;
    }



    /**
     * set value from context path
     *
     * @param string $context
     * @param mixed $value
     */
    public function setFromContext($context, $value)
    {
        $context_list = explode('.', $context);
        $context_size = count($context_list);
        $context_get_limit = $context_size - 2;

        $object = $this;
        for ($i = 0; $i <= $context_get_limit; $i++)
        {
            $method = 'get';
            $method_properties = explode('_', $context_list[$i]);
            foreach ($method_properties as $ky => $vl)
            {
                $method .= ucfirst(strtolower($vl));
            }
            if (method_exists($object, $method) === true)
            {
                $object = $object->$method();
            }
            else
            {
                $object = $object->get($context_list[$i]);
            }
        }

        if (is_array($value) === false && strtolower($value) == 'null')
        {
            $value = null;
        }

        if (empty($context_list[$i]) == true)
        {
            return ;
        }

        $method = 'set';
        $method_properties = explode('_', $context_list[$i]);
        foreach ($method_properties as $ky => $vl)
        {
            $method .= ucfirst(strtolower($vl));
        }

        if (method_exists($object, $method) === true)
        {
            if ($method == 'set')
            {
                $exist_data = $object->get($context_list[$i]);
                if (empty($exist_data) === true)
                {
                    $object->set($context_list[$i], $value);
                }
            }
            else
            {
                $object->$method($value);
            }
        }
        else
        {
            $exist_data = $object->get($context_list[$i]);
            if (empty($exist_data) === true)
            {
                $object->set($context_list[$i], $value);
            }
        }
    }
}