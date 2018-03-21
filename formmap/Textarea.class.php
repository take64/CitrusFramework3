<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap;


use Citrus\CitrusNVL;

class CitrusFormmapTextarea extends CitrusFormmapElement
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
            'type'          => 'text',
            'id'            => $this->callPrefixedId(),
            'name'          => $this->callPrefixedId(),
//            'value'         => CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()),
//            'default'       => $this->callDefault(),
            'class'         => $this->class,
            'style'         => $this->style,
            'size'          => $this->size,
            'maxlength'     => $this->max,
            'placeholder'   => $this->placeholder,
        ];
        $elements = self::appendOption($elements, $appends);

        return self::generateTag('textarea', $elements, [ CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()) ]);
    }
}