<?php
/**
 * Routing.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Configure
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Configure;


use Citrus\CitrusObject;

class CitrusConfigureRouting extends CitrusObject
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