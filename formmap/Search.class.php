<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap;


use Citrus\CitrusNVL;

class CitrusFormmapSearch extends CitrusFormmapElement
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
            'type'          => 'search',
            'id'            => $this->callPrefixedId(),
            'name'          => $this->callPrefixedId(),
            'value'         => CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()),
            'default'       => $this->callDefault(),
            'class'         => $this->class,
            'style'         => $this->style,
            'size'          => $this->size,
            'maxlength'     => $this->max,
            'placeholder'   => $this->placeholder,
        ];
        $elements = self::appendOption($elements, $appends);

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
        if (empty($value) === false)
        {
            // 変数タイプ別処理
            switch ($this->var_type)
            {
                // datetime
                case CitrusFormmapElement::VAR_TYPE_DATETIME :
                    $value = date('Y-m-d H:i:s', strtotime($this->default));
                    break;
                // date
                case CitrusFormmapElement::VAR_TYPE_DATE :
                    $value = date('Y-m-d', strtotime($this->default));
                    break;
                default:
            }
        }
        return $value;
    }
}