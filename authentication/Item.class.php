<?php
/**
 * Item.class.php.
 * 2017/08/08
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Authentication
 * @license     http://www.citrus.tk/
 * @
 */

namespace Citrus\Authentication;

use Citrus\Database\CitrusDatabaseColumn;

class CitrusAuthenticationItem extends CitrusDatabaseColumn
{
    /** @var string user id */
    public $user_id;

    /** @var string password */
    public $password;

    /** @var string token */
    public $token;

    /** @var string keep at */
    public $keep_at;
}