<?php
/**
 * Select.class.php.
 * 2017/08/14
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

class CitrusFormmapSelect extends CitrusFormmapElement
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
            'type'      => 'select',
            'id'        => $this->callPrefixedId(),
            'name'      => $this->callPrefixedId(),
            'value'     => CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()),
            'default'   => $this->callDefault(),
            'class'     => $this->class,
            'style'     => $this->style,
            'accesskey' => $this->accesskey,
        ];
        $elements = self::appendOption($elements, $appends);

        return self::generateTag('select', $elements, $this->options);
    }
}
