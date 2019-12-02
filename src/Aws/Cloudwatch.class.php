<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Aws;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Citrus\Aws;
use Citrus\Configure;
use Citrus\Struct;

class Cloudwatch extends Struct
{
    /** CloudWatchキー logGroupName */
    const LOG_GROUP_NAME = 'logGroupName';

    /** CloudWatchキー logGroupNamePrefix */
    const LOG_GROUP_NAME_PREFIX = 'logGroupNamePrefix';

    /** CloudWatchキー logStreamName */
    const LOG_STREAM_NAME = 'logStreamName';

    /** CloudWatchキー logStreamNamePrefix */
    const LOG_STREAM_NAME_PREFIX = 'logStreamNamePrefix';



    /** @var array AWS接続用パラメーター */
    protected $cloudwatch = [];

    /** @var CloudWatchLogsClient */
    protected $client;

    /** @var array 存在チェックキャッシュ */
    protected $exist_caches = [];

    /** @var array シーケンストークン配列 */
    protected $sequence_tokens = [];



    /**
     * constructor
     *
     * @param array $configure
     */
    public function __construct(array $configure = [])
    {
        // AWSセットアップ
        Aws::initialize();

        $configure = Configure::configureMerge(Aws::CONFIGURE_KEY, $configure, $configure);

        $this->bind($configure);

        $this->client = new CloudWatchLogsClient($this->cloudwatch);
    }



    /**
     * ログを送信する
     *
     * @param string   $log_group_name  ロググループ
     * @param string   $log_stream_name ログストリーム
     * @param string[] $log_events      ログイベント
     * @param bool     $with_regist     ロググループ、ログストリームも一緒に作成する
     */
    public function flush(string $log_group_name, string $log_stream_name, array $log_events, bool $with_regist = false)
    {
        // ロググループ、ログストリームも一緒に作成する
        if (true === $with_regist)
        {
            $this->registLogStream($log_group_name, $log_stream_name, true);
        }
        // 作成しない場合は、ログストリームが存在しなければ終わり
        else if (false === $this->existLogStream($log_group_name, $log_stream_name))
        {
            return;
        }

        // ログ送信パラメタ
        $params = [
            self::LOG_GROUP_NAME => $log_group_name,
            self::LOG_STREAM_NAME => $log_stream_name,
            'logEvents' => $log_events,
        ];

        // シーケンストークン
        $sequence_token = $this->callSequenceToken($log_group_name, $log_stream_name);
        if (false === is_null($sequence_token))
        {
            $params['sequenceToken'] = $sequence_token;
        }

        // ログ送信
        $response = $this->client->putLogEvents($params);

        // シーケンストークン更新
        if (false === isset($this->sequence_tokens[$log_group_name]))
        {
            $this->sequence_tokens[$log_group_name] = [];
        }
        $this->sequence_tokens[$log_group_name][$log_stream_name] = $response->get('nextSequenceToken');
    }



    /**
     * ロググループが存在するか否か
     *
     * @param string $log_group_name ロググループ名
     * @return bool
     */
    private function existLogGroup(string $log_group_name): bool
    {
        // 存在情報キャッシュがあれば利用
        if (isset($this->exist_caches[$log_group_name]) === true)
        {
            return true;
        }

        // 存在するロググループの取得
        $log_groups = $this->client
            ->describeLogGroups([self::LOG_GROUP_NAME_PREFIX => $log_group_name])
            ->get('logGroups');

        // 存在するかどうか
        foreach ($log_groups as $log_group)
        {
            if ($log_group_name === $log_group[self::LOG_GROUP_NAME])
            {
                // 存在情報をキャッシュする
                $this->exist_caches[$log_group_name] = [];
                return true;
            }
        }

        return false;
    }



    /**
     * ログストリームが存在するか否か
     *
     * @param string $log_group_name  ロググループ名
     * @param string $log_stream_name ログストリーム名
     * @return bool
     */
    private function existLogStream(string $log_group_name, string $log_stream_name): bool
    {
        // 存在チェック
        $exist_group = $this->existLogGroup($log_group_name);
        if ($exist_group === false)
        {
            return false;
        }

        // 存在情報キャッシュがあれば利用
        if (isset($this->exist_caches[$log_group_name][$log_stream_name]) === true)
        {
            return true;
        }

        // 存在するログストリームの取得
        $log_streams = $this->client
            ->describeLogStreams([
                self::LOG_GROUP_NAME => $log_group_name,
                self::LOG_STREAM_NAME_PREFIX => $log_stream_name,
                ])
            ->get('logStreams');

        // 存在するかどうか
        foreach ($log_streams as $log_stream)
        {
            if ($log_stream_name === $log_stream[self::LOG_STREAM_NAME])
            {
                // 存在情報をキャッシュする
                $this->exist_caches[$log_group_name][$log_stream_name] = true;
                return true;
            }
        }

        return false;
    }



    /**
     * ロググループの作成
     *
     * @param string $log_group_name ロググループ名
     */
    private function registLogGroup(string $log_group_name)
    {
        // すでにあれば作らない
        if ($this->existLogGroup($log_group_name) === true)
        {
            return;
        }

        // ロググループの作成
        $this->client->createLogGroup([self::LOG_GROUP_NAME => $log_group_name]);
    }



    /**
     * ログストリームの作成
     *
     * @param string $log_group_name  ロググループ名
     * @param string $log_stream_name ログストリーム名
     * @param bool   $with_group      ログストリームと一緒にロググループも作る
     */
    private function registLogStream(string $log_group_name, string $log_stream_name, bool $with_group = false)
    {
        // ログストリームと一緒にロググループも作る
        if ($with_group === true)
        {
            $this->registLogGroup($log_group_name);
        }

        // すでにあれば作らない
        if ($this->existLogStream($log_group_name, $log_stream_name) === true)
        {
            return;
        }

        // ログストリームの作成
        $this->client->createLogStream([
            self::LOG_GROUP_NAME => $log_group_name,
            self::LOG_STREAM_NAME => $log_stream_name,
            ]);
    }



    /**
     * シーケンストークンの取得
     *
     * @param string $log_group_name  ロググループ名
     * @param string $log_stream_name ログストリーム名
     * @return null
     */
    private function callSequenceToken(string $log_group_name, string $log_stream_name)
    {
        // シーケンストークンがあれば返す
        if (true === isset($this->sequence_tokens[$log_group_name])
            && true === isset($this->sequence_tokens[$log_group_name][$log_stream_name]))
        {
            return $this->sequence_tokens[$log_group_name][$log_stream_name];
        }

        // シーケンストークンを取得する
        $log_streams = $this->client
            ->describeLogStreams([
                self::LOG_GROUP_NAME => $log_group_name,
                self::LOG_STREAM_NAME_PREFIX => $log_stream_name,
            ])
            ->get('logStreams');

        // 存在するかどうか
        foreach ($log_streams as $log_stream)
        {
            if ($log_stream_name === $log_stream[self::LOG_STREAM_NAME]
                && true === isset($log_stream['uploadSequenceToken']))
            {
                $sequence_token = $log_stream['uploadSequenceToken'];
                $this->sequence_tokens[$log_group_name][$log_stream_name] = $sequence_token;
                return $sequence_token;
            }
        }

        return null;
    }
}
