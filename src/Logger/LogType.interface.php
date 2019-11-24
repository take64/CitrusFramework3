<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;

interface LogType
{
    /**
     * output
     *
     * @param string $level  ログレベル
     * @param mixed  $value  ログ内容
     * @param array  $params パラメーター
     */
    public function output(string $level, $value, array $params = []);
}
