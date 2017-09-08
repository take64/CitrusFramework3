<?php
/**
 * Element.class.php.
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


class CitrusXhrElement
{
    /** @var CitrusXhrResult result object */
    public $results = null;

    /** @var CitrusMessageElement[] message */
    public $messages = [];

    /** @var CitrusException[] exception */
    public $exceptions = [];
}