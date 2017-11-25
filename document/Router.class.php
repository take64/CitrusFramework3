<?php
/**
 * Router.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Document
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Document;

use Citrus\CitrusNVL;
use Citrus\CitrusObject;
use Citrus\CitrusUseragent;
use Citrus\Useragent\CitrusUseragentDevice;

class CitrusDocumentRouter extends CitrusObject
{
    /** @var string */
    CONST ROUTE_DEFAULT = 'default';

    /** @var string */
    CONST ROUTE_LOGIN = 'login';

    /** @var string */
    CONST ROUTE_ERROR404 = 'error404';

    /** @var string */
    CONST ROUTE_ERROR503 = 'error503';

    /** @var string */
    public $device;

    /** @var string */
    public $document;

    /** @var string */
    public $action;

    /** @var array */
    public $parameters;

    /** @var array */
    public static $DEVICE_ROUTING = [];

    /** @var array */
    public static $ACCESS_ROUTING = [];

    /** @var bool */
    public static $IS_INITIALIZED = false;



    /**
     * initialize router
     *
     * @param array $default_configure
     * @param array $configure
     */
    public static function initialize(array $default_configure = [], array $configure = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // デバイス一覧
        $device_list = CitrusUseragentDevice::callDeviceList();

        // デバイス設定
        $devices = [];
        $devices = array_merge($devices, CitrusNVL::ArrayVL($default_configure, 'device', []));
        $devices = array_merge($devices, CitrusNVL::ArrayVL($configure, 'device', []));

        // デバイスルーティング設定
        foreach ($device_list as $one)
        {
            if (isset($devices[$one]) === true)
            {
                self::$DEVICE_ROUTING[$one] = $devices[$one];
            }
        }

        // アクセス設定
        $accesses = [];
        $accesses = array_merge($accesses, CitrusNVL::ArrayVL($default_configure, 'routing', []));
        $accesses = array_merge($accesses, CitrusNVL::ArrayVL($configure, 'routing', []));
        self::$ACCESS_ROUTING = $accesses;

        // initialized
        self::$IS_INITIALIZED = true;
    }



    /**
     * call default URL
     *
     * @return  string
     */
    public static function callDefaultURL()
    {
        return self::$ACCESS_ROUTING[self::ROUTE_DEFAULT];
    }



    /**
     * call login URL
     *
     * @return  string
     */
    public static function callLoginURL()
    {
        return self::$ACCESS_ROUTING[self::ROUTE_LOGIN];
    }



    /**
     * url parse
     *
     * @param string|null $url
     * @return CitrusDocumentRouter
     */
    public static function parseURL(string $url = null)
    {
        // ルータ
        $router = new CitrusDocumentRouter();

        // 分割
        $routers = explode('/', $url);

        // /で終わっている場合を考慮
        if (count($routers) > 0)
        {
            $last_index = count($routers) - 1;
            if (empty($routers[$last_index]) === true)
            {
                unset($routers[$last_index]);
            }
        }

        // /で始まっている場合を考慮
        if (count($routers) > 0)
        {
            if (empty($routers[0]) === true)
            {
                unset($routers[0]);
            }
        }

        // 添え字振り直し
        $routers = array_merge($routers);

        // prefix が device 設定にある場合
        if (in_array($routers[0], self::$DEVICE_ROUTING) === true)
        {
            $router->device = array_shift($routers);
        }
        // useragent から device 設定を取得
        else
        {
            $useragent = CitrusUseragent::vague();
            $router->device = self::$DEVICE_ROUTING[$useragent->device];
        }

        // ルーティング要素が１つしか無い場合はデフォルトでindexをつける
        if (count($routers) === 1)
        {
            $routers[] = 'index';
        }

        // 最終要素がactionになる
        $router->action = array_pop($routers);

        // 残った要素がdocumentになる
        $router->document = implode('-', $routers);

        return $router;
    }



    /**
     * factory
     *
     * @param array|null $request
     * @return CitrusDocumentRouter
     */
    public static function factory(array $request = null)
    {
        if (isset($request['url']) === false)
        {
            $request = ['url' => self::callDefaultURL()];
        }
        $router = self::parseURL($request['url']);
        foreach ($request as $ky => $vl)
        {
            if ($ky == 'url')
            {
                continue;
            }
            $router->add('parameters', [$ky => $vl]);
        }

        return $router;
    }



    /**
     * redirect URL
     *
     * @param string|null $url
     */
    public static function redirectURL(string $url = null)
    {
        if ($url != null)
        {
            header('Location: '. $url);
            exit;
        }
    }
}