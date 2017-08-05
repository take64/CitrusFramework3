<?php
/**
 * Result.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Database
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;


class CitrusDatabaseResult extends CitrusDatabaseColumn
{
    /** @var int count */
    public $count;

    /** @var int sum */
    public $sum;

    /** @var int avg */
    public $avg;

    /** @var int max */
    public $max;

    /** @var int min */
    public $min;

    /** @var int id */
    public $id;

    /** @var string name */
    public $name;
}