<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Result;


use Citrus\Database\CitrusDatabaseColumn;

class CitrusDatabaseResultYearmonth extends CitrusDatabaseColumn
{
    /** @var int year */
    public $year;

    /** @var int month */
    public $month;

    /** @var float result */
    public $result;
}
