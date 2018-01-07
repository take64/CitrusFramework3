<?php
/**
 * Password.class.php.
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

class CitrusFormmapPassword extends CitrusFormmapElement
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
            'type'      => 'password',
            'id'        => $this->callPrefixedId(),
            'name'      => $this->callPrefixedId(),
            'value'     => CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()),
            'class'     => $this->class,
            'style'     => $this->style,
            'size'      => $this->size,
            'maxlength' => $this->max,
        ];
        $elements = self::appendOption($elements, $appends);

        return self::generateTag('input', $elements);
    }
}