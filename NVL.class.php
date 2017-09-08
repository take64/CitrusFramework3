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
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Closure;

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
     * NVL coalesce
     *
     * @param array ...$_
     * @return mixed|null
     */
    public static function coalesce(...$_)
    {
        $result = null;
        $replacements = func_get_args();

        foreach ($replacements as $replacement)
        {
            if (is_null($replacement) === true)
            {
                continue;
            }

            // クロージャなら実行
            if ($replacement instanceof Closure)
            {
                $result = $replacement();
            }
            else
            {
                $result = $replacement;
            }
            break;
        }

        return $result;
    }



    /**
     * ArrayVL
     *
     * @param array  $value
     * @param mixed  $key
     * @param mixed  $replace
     * @return array|mixed
     */
    public static function ArrayVL(array $value, $key, $replace)
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
     * @param mixed $replace1
     * @param mixed $replace2
     * @return array|mixed|Closure|null
     */
    public static function EmptyVL($value, $replace1, $replace2)
    {
        $replace = null;
        if (empty($value) === true)
        {
            $replace = $replace1;
        }
        else
        {
            $replace = $replace2;
        }

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
}
