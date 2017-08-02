<?php
/**
 * NVL.class.php.
 * null value logic
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


class CitrusNVL
{
    /**
     * NVL
     *
     * @param mixed $value
     * @param mixed $replace
     * @return mixed
     */
    public static function NVL($value, $replace)
    {
        if (is_null($value) === false)
        {
            return $value;
        }
        return $replace;
    }



    /**
     * NVL replace
     *
     * @param mixed $value
     * @param mixed $replace1
     * @param mixed $replace2
     * @return mixed
     */
    public static function replace($value, $replace1, $replace2)
    {
        if (is_null($value) === false)
        {
            return $replace1;
        }
        return $replace2;
    }



    /**
     * ArrayVL
     *
     * @param array  $value
     * @param string $key
     * @param mixed  $replace
     * @return array|mixed
     */
    public static function ArrayVL(array $value, string $key, $replace)
    {
        $value = self::NVL($value, $replace);

        $result = $replace;

        if (is_array($value) === true)
        {
            if (isset($value[$key]) === true)
            {
                $result = $value[$key];
            }
        }

        return $result;
    }



    /**
     * EmptyVL
     *
     * @param mixed $value
     * @param mixed $replace
     * @return array|mixed|Closure|null
     */
    public static function EmptyVL($value, $replace)
    {
        if (empty($value) === true)
        {
            // クロージャも許容
            if ($replace instanceof \Closure)
            {
                return $replace();
            }
            else
            {
                return $replace;
            }
        }

        return $value;
    }
}
