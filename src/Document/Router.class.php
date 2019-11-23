<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Document;

use Citrus\Configure;
use Citrus\Struct;
use Citrus\Useragent;
use Citrus\Useragent\Device;

class Router extends Struct
{
    /** CitrusConfigureキー */
    const CONFIGURE_DEVICE_KEY = 'device';

    /** CitrusConfigureキー */
    const CONFIGURE_ROUTING_KEY = 'routing';

    /** @var string */
    const ROUTE_DEFAULT = 'default';

    /** @var string */
    const ROUTE_LOGIN = 'login';

    /** @var string */
    const ROUTE_ERROR404 = 'error404';

    /** @var string */
    const ROUTE_ERROR503 = 'error503';

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
     * @param array $configure_domain
     */
    public static function initialize(array $default_configure = [], array $configure_domain = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return;
        }

        // デバイス一覧
        $device_list = Device::callDeviceList();

        // デバイス設定
        $configure_devices = Configure::configureMerge(self::CONFIGURE_DEVICE_KEY, $default_configure, $configure_domain);

        // デバイスルーティング設定
        foreach ($device_list as $one)
        {
            if (isset($configure_devices[$one]) === true)
            {
                self::$DEVICE_ROUTING[$one] = $configure_devices[$one];
            }
        }

        // アクセス設定
        $configure_accesses = Configure::configureMerge(self::CONFIGURE_ROUTING_KEY, $default_configure, $configure_domain);
        self::$ACCESS_ROUTING = $configure_accesses;

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
     * @return Router
     */
    public static function parseURL(string $url = null)
    {
        // ルータ
        $router = new Router();

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
        if (count($routers) > 0 && empty($routers[0]) === true)
        {
            unset($routers[0]);
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
            $useragent = Useragent::vague();
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
     * @return Router
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
            header('Location: ' . $url);
            exit;
        }
    }
}