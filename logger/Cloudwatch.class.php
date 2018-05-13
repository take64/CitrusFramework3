<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Logger;


use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Citrus\Aws\CitrusAwsCloudwatch;
use Citrus\CitrusObject;

class CitrusLoggerCloudwatch extends CitrusObject implements CitrusLoggerType
{
    /** @var CitrusAwsCloudwatch クラウドウォッチ */
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

        $this->cloudwatch = new CitrusAwsCloudwatch();
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
     * @param mixed $value
     * @param array $params
     */
    public function output($value, array $params = [])
    {
        if (is_string($value) === true)
        {
            $vl_dump = vsprintf($value, $params) . PHP_EOL;
        }
        else
        {
            ob_start();
            var_dump($value);
            $vl_dump = ob_get_contents();
            ob_end_clean();
        }

        $this->buffers[] = [
            'message' => htmlspecialchars_decode(strip_tags($vl_dump)),
            'timestamp' => ($_SERVER['REQUEST_TIME_FLOAT'] * 1000),
        ];

        clearstatcache();

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
