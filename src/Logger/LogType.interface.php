<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;

/**
 * ログタイプ
 */
interface LogType
{
    /** @var string file */
    const FILE = 'file';

    /** @var string syslog */
    const SYSLOG = 'syslog';

    /** @var string CloudWatchLogs */
    const CLOUDWATCHLOGS = 'cloudwatchlogs';

    /**
     * output
     *
     * @param string $level  ログレベル
     * @param mixed  $value  ログ内容
     * @param array  $params パラメーター
     */
    public function output(string $level, $value, array $params = []);
}
