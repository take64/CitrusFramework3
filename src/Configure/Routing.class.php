<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Configure;

use Citrus\Struct;

class Routing extends Struct
{
    /** @var string */
    public $default;

    /** @var string */
    public $login;

    /** @var string */
    public $error404;

    /** @var string */
    public $error503;
}
