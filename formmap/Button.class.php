<?php
/**
 * @copyright   Copyright 2018, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 * @
 */

namespace Citrus\Formmap;


class CitrusFormmapButton extends CitrusFormmapElement
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
            'type'      => 'button',
            'id'        => $this->callPrefixedId(),
            'name'      => $this->callPrefixedId(),
            'value'     => $this->name,
            'class'     => $this->class,
            'style'     => $this->style,
            'accesskey' => $this->accesskey,
        ];
        $elements = self::appendOption($elements, $appends);

        return self::generateTag('button', $elements, $this->name);
    }
}