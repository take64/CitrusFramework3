<?php
/**
 * Text.class.php.
 * 2017/08/06
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Formmap
 * @license     http://www.citrus.tk/
 * @
 */

namespace Citrus\Formmap;


use Citrus\CitrusNVL;

class CitrusFormmapText extends CitrusFormmapElement
{
    /**
     * to string
     *
     * @param array $appends
     * @return string
     */
    public function toString(array $appends = [])
    {
        $elements = [
            'id'        => $this->id,
            'name'      => $this->name,
            'value'     => CitrusNVL::replace($this->value, $this->callValue(), $this->callDefault()),
            'class'     => $this->class,
            'style'     => $this->style,
            'size'      => $this->size,
            'maxlength' => $this->max,
        ];
        $elements = array_merge($elements, $this->options, $appends);

        return self::generateTag('input', $elements);
    }



    /**
     * call default value
     *
     * @return false|mixed|string
     */
    public function callDefault()
    {
        $value = $this->default;

        // デフォルト設定
        if(empty($value) === false)
        {
            // 変数タイプ別処理
            switch ($this->var_type)
            {
                // datetime
                case CitrusFormElement::VAR_TYPE_DATETIME :
                    $value = date('Y-m-d H:i:s', strtotime($this->default));
                    break;
                // date
                case CitrusFormElement::VAR_TYPE_DATE :
                    $value = date('Y-m-d', strtotime($this->default));
                    break;
            }
        }
        return $value;
    }
}