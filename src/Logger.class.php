<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Configure\Configurable;
use Citrus\Configure\ConfigureException;
use Citrus\Logger\Cloudwatch;
use Citrus\Logger\File;
use Citrus\Logger\Level;
use Citrus\Logger\Syslog;
use Citrus\Logger\LogType;
use Citrus\Variable\Singleton;

/**
 * ログ処理
 */
class Logger extends Configurable
{
    use Singleton;

    /** @var string logger type file */
    const LOG_TYPE_FILE = 'file';

    /** @var string logger type php syslog */
    const LOG_TYPE_SYSLOG = 'syslog';

    /** @var string logger type cloudwatch */
    const LOG_TYPE_CLOUDWATCH = 'cloudwatch';

    /** @var LogType ログタイプ別のインスタンス */
    public $logType;



    /**
     * 初期化
     *
     * @param array $configures 設定配列
     * @return void
     * @throws ConfigureException|\Exception
     */
    public static function initialize(array $configures = []): void
    {
        /** @var Logger $logger インスタンス */
        $logger = self::getInstance();
        // 設定読み込み
        $logger->loadConfigures($configures);

        // タイプによって生成インスタンスを分ける
        switch ($logger->configures['type'])
        {
            // file
            case self::LOG_TYPE_FILE:
                $logger->logType = new File($logger->configures);
                break;
            // syslog
            case self::LOG_TYPE_SYSLOG:
                $logger->logType = new Syslog($logger->configures);
                break;
            // AWS CloudWatch
            case self::LOG_TYPE_CLOUDWATCH:
                $logger->logType = new Cloudwatch($logger->configures);
                break;
            default:
                throw new \Exception('Loggerの生成に失敗しました');
        }
    }



    /**
     * trace log file
     *
     * @param mixed $value
     */
    public static function trace($value)
    {
        self::getInstance()->output(Level::TRACE, $value, func_get_args());
    }



    /**
     * debug log file
     *
     * @param mixed $value
     */
    public static function debug($value)
    {
        self::getInstance()->output(Level::DEBUG, $value, func_get_args());
    }



    /**
     * info log file
     *
     * @param mixed $value
     */
    public static function info($value)
    {
        self::getInstance()->output(Level::INFO, $value, func_get_args());
    }



    /**
     * warn log file
     *
     * @param mixed $value
     */
    public static function warn($value)
    {
        self::getInstance()->output(Level::WARNING, $value, func_get_args());
    }



    /**
     * error log file
     *
     * @param mixed $value
     */
    public static function error($value)
    {
        self::getInstance()->output(Level::ERROR, $value, func_get_args());
    }



    /**
     * fatal log file
     *
     * @param mixed $value
     */
    public static function fatal($value)
    {
        self::getInstance()->output(Level::FATAL, $value, func_get_args());
    }



    /**
     * output log file
     *
     * @param string $level  ログレベル
     * @param mixed  $value  ログの内容
     * @param array  $params パラメータ
     * @return void
     */
    public function output(string $level, $value, array $params): void
    {
        // ログレベルによる出力許容チェック
        if (false === self::isOutputableLevel($level))
        {
            return;
        }

        // params
        array_shift($params);

        // display
        if (true === $this->configures['display'])
        {
            $display_value = $value;
            if (true === is_string($value))
            {
                $display_value = vsprintf($value, $params);

            }
            var_dump([
                Citrus::$TIMESTAMP_FORMAT,
                $display_value,
            ]);

        }
        $this->logType->output($level, $value, $params);
    }



    /**
     * コンフィグ設定で指定されたログを出力するレベルか判定する
     *
     * @param string $level ログレベル
     * @return bool
     */
    public function isOutputableLevel(string $level): bool
    {
        // 出力設定のログレベル
        $configure_level_index = array_search($this->configures['level'], Level::$LEVELS, true);
        // 出力しようとしているログレベル
        $target_level_index = array_search($level, Level::$LEVELS, true);
        // 出力設定のログレベル <= 出力しようとしているログレベル
        return ($configure_level_index <= $target_level_index);
    }



    /**
     * {@inheritDoc}
     */
    protected function configureKey(): string
    {
        return 'logger';
    }



    /**
     * {@inheritDoc}
     */
    protected function configureDefaults(): array
    {
        // ロガータイプ
        $type = ($this->configures['type'] ?? null);

        // 共通
        $defaults = [
            'level'   => Level::INFO,
            'display' => false,
        ];

        // ファイルの場合
        if (self::LOG_TYPE_FILE === $type)
        {
            $defaults += [
                'owner' => posix_getpwuid(posix_geteuid())['name'],
                'group' => posix_getgrgid(posix_getegid())['name'],
            ];
        }

        return $defaults;
    }



    /**
     * {@inheritDoc}
     */
    protected function configureRequires(): array
    {
        // ロガータイプ
        $type = ($this->configures['type'] ?? null);

        // 共通
        $requires = [
            'type',
        ];

        // ファイルの場合
        if (self::LOG_TYPE_FILE === $type)
        {
            $requires += [
                'directory',
                'filename',
                'owner',
                'group',
            ];
        }

        return $requires;
    }
}
