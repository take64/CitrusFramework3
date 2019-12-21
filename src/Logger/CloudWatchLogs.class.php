<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Citrus\Variable\Dates;
use Citrus\Variable\Structs;

/**
 * CloudWatchLogs出力ロガー
 */
class CloudWatchLogs implements LogType
{
    use Structs;

    /** @var \Citrus\Integration\Aws\CloudwatchLogs クラウドウォッチ */
    protected $cloudwatchLogs;

    /** @var array AWS接続用パラメーター */
    protected $aws = [];

    /** @var bool ログ内容をバッファリングして貯める */
    protected $is_buffering = true;

    /** @var string[] ログバッファ */
    protected $buffers = [];

    /** @var CloudWatchLogsClient */
    protected $client;

    /** @var string ロググループ */
    protected $group = '';

    /** @var string ログストリーム */
    protected $stream = '';



    /**
     * constructor
     *
     * @param array $configure
     */
    public function __construct(array $configure = [])
    {
        $this->bind($configure);

        $this->cloudwatchLogs = new \Citrus\Integration\Aws\CloudwatchLogs();
    }



    /**
     * destructor
     */
    public function __destruct()
    {
        $this->flush();
    }



    /**
     * output log file
     *
     * @param string $level  ログレベル
     * @param mixed  $value  ログ内容
     * @param array  $params パラメーター
     * @return void
     */
    public function output(string $level, $value, array $params = []): void
    {
        if (true === is_string($value))
        {
            $value = vsprintf($value, $params) . PHP_EOL;
        }

        $format = [
            'messages' => $value,
            'level' => $level,
            'datetime' => Dates::now(),
        ];

        $this->buffers[] = [
            'message' => json_encode($format),
            'timestamp' => ($_SERVER['REQUEST_TIME_FLOAT'] * 1000),
        ];

        // バッファリングしない場合は、すぐに送る
        if (false === $this->is_buffering)
        {
            $this->flush();
        }
    }



    /**
     * ログ書き出し
     *
     * @return void
     */
    private function flush(): void
    {
        // バッファがない場合はスルー
        if (true === empty($this->buffers))
        {
            return;
        }

        $this->cloudwatchLogs->flush($this->group, $this->stream, $this->buffers, true);
        $this->buffers = [];
    }
}
