<?php
/**
 * Progress.class.php.
 * 2017/09/07
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  process
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Process;


class CitrusProcessProgress
{
    /** @var int total count */
    public $total_count = 0;

    /** @var int regist count */
    public $regist_count = 0;

    /** @var int modify count */
    public $modify_count = 0;



    /**
     * 登録・更新の情報
     *
     * @param int $total
     * @param int $regist
     * @param int $modify
     * @return CitrusProcessProgress
     */
    public static function generateEntry(int $total = 0, int $regist = 0, int $modify = 0) : CitrusProcessProgress
    {
        $progress = new static();

        $progress->total_count = $total;
        $progress->regist_count = $regist;
        $progress->modify_count = $modify;

        return $progress;
    }
}