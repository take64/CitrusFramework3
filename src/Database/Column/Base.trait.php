<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Column;

trait Base
{
    /** @var string status */
    public $status = 0;

    /** @var string registed_at */
    public $registed_at;

    /** @var string modified_at */
    public $modified_at;

    /** @var string rowid */
    public $rowid;

    /** @var string rev */
    public $rev;
}