<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Citrus\Struct;

class Cloudwatch extends Struct implements LogType
{
    /** @var \Citrus\Aws\Cloudwatch クラウドウォッチ */
    protected $cloudwatch;

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

        $this->cloudwatch = new \Citrus\Aws\Cloudwatch();
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
     */
    public function output(string $level, $value, array $params = [])
    {
        if (is_string($value) === true)
        {
            $value = vsprintf($value, $params) . PHP_EOL;
        }

        $format = [
            'messages' => $value,
            'level' => $level,
            'datetime' => new \DateTime(),
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
     */
    private function flush()
    {
        // バッファがない場合はスルー
        if (true === empty($this->buffers))
        {
            return ;
        }

        $this->cloudwatch->flush($this->group, $this->stream, $this->buffers, true);
        $this->buffers = [];
    }
}
