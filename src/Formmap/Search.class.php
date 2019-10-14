<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap;


use Citrus\NVL;

class Search extends Text
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
            'value'         => NVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()),
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
}