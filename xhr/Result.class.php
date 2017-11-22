<?php
/**
 * Result.class.php.
 * 2017/08/25
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Xhr
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Xhr;


use Citrus\Document\CitrusDocumentPager;

class CitrusXhrResult
{
    /** @var bool result */
    public $result = false;

    /** @var array result object */
    public $items = [];

    /** @var CitrusDocumentPager pager  */
    public $pager = null;



    /**
     * constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        if (empty($items) === false || is_array($items) === true)
        {
            $this->result = true;
        }
        $this->items = $items;
    }
}