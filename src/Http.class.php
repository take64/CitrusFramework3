<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

class Http
{
    /** @var string */
    const METHOD_GET = 'get';

    /** @var string */
    const METHOD_POST = 'post';



    /**
     * getリクエスト
     *
     * @param string $url
     * @param array  $parameters
     * @return string
     */
    public static function get(string $url, array $parameters = []) : string
    {
        return self::request($url, self::METHOD_GET, $parameters);
    }



    /**
     * postリクエスト
     *
     * @param string $url
     * @param mixed  $parameters
     * @return string
     */
    public static function post(string $url, $parameters = []) : string
    {
        return self::request($url, self::METHOD_POST, $parameters);
    }



    /**
     * リクエストを送る
     *
     * @param string $url
     * @param string $method
     * @param mixed  $parameters
     * @return string
     */
    private static function request(string $url, string $method, $parameters) : string
    {
        // 接続開始
        $handle = curl_init();

        // リクエストパラメータ(GET)
        if ($method === self::METHOD_GET)
        {
            $url = sprintf('%s?%s',
                $url,
                http_build_query($parameters)
                );
        }

        // URL
        curl_setopt($handle, CURLOPT_URL,   $url);
        curl_setopt($handle, CURLOPT_HEADER, 0);

        // リクエストパラメータ(POST)
        if ($method === self::METHOD_POST)
        {
            curl_setopt($handle, CURLOPT_POST, true);
            if (is_array($parameters) === true)
            {
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($parameters));
            }
            else if (is_string($parameters) === true)
            {
                curl_setopt($handle, CURLOPT_POSTFIELDS, $parameters);
            }
        }

        // アクセスの結果を文字列で返却する
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        // Locationヘッダが有る場合に追跡する
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);

        // COOKIE
        curl_setopt($handle, CURLOPT_COOKIEJAR, 'cookie');
        curl_setopt($handle, CURLOPT_COOKIEFILE, 'tmp');

        // 実行
        $result = curl_exec($handle);

        // 接続終了
        curl_close($handle);

        return $result;
    }
}