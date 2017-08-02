<?php
/**
 * Application.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     .
 * @subpackage  .
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Configure;

use Citrus\CitrusObject;

class CitrusConfigureApplication extends CitrusObject
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