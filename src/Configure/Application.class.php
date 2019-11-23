<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Configure;

use Citrus\Struct;

class Application extends Struct
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $path;

    /** @var string */
    public $copyright;

    /** @var string */
    public $domain;
}
