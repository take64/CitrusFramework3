<?php
/**
 * Hidden.class.php.
 * 2017/08/18
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

class CitrusFormmapHidden extends CitrusFormmapElement
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
            'type'      => 'hidden',
            'id'        => $this->callPrefixedId(),
            'name'      => $this->callPrefixedId(),
            'value'     => CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()),
            'class'     => $this->class,
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
                // default
                default :
                    break;
            }
        }
        return $value;
    }
}