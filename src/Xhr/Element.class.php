<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Xhr;

use Citrus\CitrusException;
use Citrus\Message\Item;

class Element
{
    /** @var Result result object */
    public $results = null;

    /** @var Item[] message */
    public $messages = [];

    /** @var CitrusException[] exception */
    public $exceptions = [];
}